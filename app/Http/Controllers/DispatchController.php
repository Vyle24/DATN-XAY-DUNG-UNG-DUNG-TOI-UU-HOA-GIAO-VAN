<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shipper;
use App\Models\Route;
use App\Models\RouteOrder;
use App\Models\ShipperAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DispatchController extends Controller
{
    // ============================================================
    // DASHBOARD: Hiển thị trang điều phối tổng hợp
    // ============================================================
    public function index()
    {
        // Đơn hàng chưa giao, chưa gán (unassigned pending)
        $unassignedOrders = Order::where('assignment_status', 'unassigned')
            ->whereIn('status', ['pending'])
            ->whereNotNull('delivery_lat')
            ->whereNotNull('delivery_lng')
            ->with(['customer.user', 'hub'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Shipper đang online
        $activeShippers = Shipper::where('is_active', true)
            ->with(['user', 'orders' => function ($q) {
                $q->where('status', 'processing');
            }])
            ->get()
            ->map(function ($s) {
                $s->active_order_count = $s->orders->count();
                return $s;
            });

        // Tuyến đường hiện có (chỉ lấy các tuyến còn đơn hàng đang chờ hoặc đang giao)
        $routes = Route::whereHas('routeOrders.order', function ($q) {
                $q->whereNotIn('status', ['delivered', 'failed', 'cancelled']);
            })
            ->with(['shipper.user', 'routeOrders.order'])
            ->orderBy('id', 'desc')
            ->get();

        // Thống kê
        $stats = [
            'unassigned'       => $unassignedOrders->count(),
            'active_shippers'  => $activeShippers->count(),
            'active_routes'    => $routes->count(),
            'total_processing' => Order::where('status', 'processing')->count(),
        ];

        return view('admin.dispatch', compact('unassignedOrders', 'activeShippers', 'routes', 'stats'));
    }

    // ============================================================
    // 1. BATCH DELIVERY — Gom đơn hàng gần nhau
    // ============================================================
    public function batchOrders(Request $request)
    {
        $request->validate([
            'radius_km'  => 'required|numeric|min:0.5|max:50',
            'max_orders' => 'required|integer|min:2|max:20',
        ]);

        $radiusKm  = (float) $request->radius_km;
        $maxOrders = (int) $request->max_orders;

        // Lấy tất cả đơn chưa gán có tọa độ
        $orders = Order::where('assignment_status', 'unassigned')
            ->where('status', 'pending')
            ->whereNotNull('delivery_lat')
            ->whereNotNull('delivery_lng')
            ->get();

        if ($orders->count() < 2) {
            return back()->with('error', 'Cần ít nhất 2 đơn hàng chưa gán để gom batch!');
        }

        // Thuật toán gom nhóm: Greedy clustering theo khoảng cách GPS
        $batches   = [];
        $assigned  = [];

        foreach ($orders as $order) {
            if (in_array($order->id, $assigned)) continue;

            $batch = [$order];
            $assigned[] = $order->id;

            foreach ($orders as $other) {
                if (in_array($other->id, $assigned)) continue;
                if (count($batch) >= $maxOrders) break;

                $dist = $this->haversineKm(
                    (float)$order->delivery_lat, (float)$order->delivery_lng,
                    (float)$other->delivery_lat, (float)$other->delivery_lng
                );

                if ($dist <= $radiusKm) {
                    $batch[]    = $other;
                    $assigned[] = $other->id;
                }
            }

            if (count($batch) >= 2) {
                $batches[] = $batch;
            }
        }

        if (empty($batches)) {
            return back()->with('error', "Không tìm thấy nhóm đơn nào trong bán kính {$radiusKm} km. Thử tăng bán kính.");
        }

        // Tạo routes cho mỗi batch (chưa gán shipper)
        $routeCount = 0;
        DB::transaction(function () use ($batches, &$routeCount) {
            foreach ($batches as $batch) {
                // Tối ưu thứ tự ngay khi tạo batch (Nearest Neighbor)
                $ordered = $this->nearestNeighborSort($batch);

                // Tính tổng khoảng cách batch
                $totalDist = $this->calcTotalDistance($ordered);

                $route = Route::create([
                    'shipper_id'     => null,
                    'total_distance' => round($totalDist, 2),
                    'estimated_time' => round($totalDist * 3 + count($ordered) * 5), // ~3 min/km + 5 min/stop
                    'optimized_path' => [],
                ]);

                foreach ($ordered as $seq => $order) {
                    RouteOrder::create([
                        'route_id'      => $route->id,
                        'order_id'      => $order->id,
                        'stop_sequence' => $seq + 1,
                        'estimated_arrival_time' => now()->addMinutes(($seq + 1) * (round($totalDist * 3 / count($ordered)) + 5)),
                    ]);
                    // Không đổi assignment_status vì chưa gán shipper
                }
                $routeCount++;
            }
        });

        return back()->with('success', "Đã gom thành công {$routeCount} batch delivery! Tiến hành Auto-Assign Shipper để phân công.");
    }

    // ============================================================
    // 2. ROUTE OPTIMIZATION — Tối ưu thứ tự trong một tuyến
    // ============================================================
    public function optimizeRoute(Request $request, $routeId)
    {
        $route = Route::with('routeOrders.order')->findOrFail($routeId);

        $orders = $route->routeOrders->map(fn($ro) => $ro->order)->filter(fn($o) => $o && $o->delivery_lat && $o->delivery_lng)->values();

        if ($orders->count() < 2) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tuyến cần ít nhất 2 điểm dừng để tối ưu!'
                ], 400);
            }
            return back()->with('error', 'Tuyến cần ít nhất 2 điểm dừng để tối ưu!');
        }

        // Nearest Neighbor Algorithm
        $optimized = $this->nearestNeighborSort($orders->all());
        $totalDist = $this->calcTotalDistance($optimized);

        DB::transaction(function () use ($route, $optimized, $totalDist) {
            // Cập nhật stop_sequence và estimated_arrival_time
            foreach ($optimized as $seq => $order) {
                $minutesFromNow = ($seq + 1) * (round($totalDist * 3 / count($optimized)) + 5);
                RouteOrder::where('route_id', $route->id)
                    ->where('order_id', $order->id)
                    ->update([
                        'stop_sequence'          => $seq + 1,
                        'estimated_arrival_time' => now()->addMinutes($minutesFromNow),
                    ]);
            }

            // Cập nhật tổng quãng đường và thời gian
            $route->update([
                'total_distance' => round($totalDist, 2),
                'estimated_time' => round($totalDist * 3 + count($optimized) * 5),
                'optimized_path' => array_map(fn($o) => ['lat' => $o->delivery_lat, 'lng' => $o->delivery_lng], $optimized),
            ]);
        });

        if ($request->wantsJson() || $request->ajax()) {
            $route->load(['routeOrders.order', 'shipper.user']);
            $routeOrders = $route->routeOrders->sortBy('stop_sequence');
            $points = [];
            
            $firstRo = $routeOrders->first();
            if ($firstRo && $firstRo->order && $firstRo->order->hub && $firstRo->order->hub->latitude && $firstRo->order->hub->longitude) {
                $points[] = [
                    'type' => 'hub',
                    'name' => $firstRo->order->hub->name,
                    'lat' => (float)$firstRo->order->hub->latitude,
                    'lng' => (float)$firstRo->order->hub->longitude,
                    'code' => 'HUB'
                ];
            }
            
            foreach ($routeOrders as $ro) {
                $ord = $ro->order;
                if ($ord) {
                    if ($ord->pickup_lat && $ord->pickup_lng) {
                        $points[] = [
                            'type' => 'pickup',
                            'name' => 'Nhận: ' . $ord->pickup_address,
                            'lat' => (float)$ord->pickup_lat,
                            'lng' => (float)$ord->pickup_lng,
                            'code' => $ord->order_code
                        ];
                    }
                    if ($ord->delivery_lat && $ord->delivery_lng) {
                        $points[] = [
                            'type' => 'delivery',
                            'name' => 'Giao: ' . $ord->delivery_address,
                            'lat' => (float)$ord->delivery_lat,
                            'lng' => (float)$ord->delivery_lng,
                            'code' => $ord->order_code
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Đã tối ưu tuyến #{$routeId} bằng Nearest Neighbor! Tổng quãng đường: " . round($totalDist, 2) . " km.",
                'route' => [
                    'id' => $route->id,
                    'shipper' => $route->shipper->user->username ?? 'Chưa gán',
                    'points' => $points,
                    'stops' => $routeOrders->map(fn($ro) => [
                        'seq' => $ro->stop_sequence,
                        'lat' => (float)($ro->order->delivery_lat ?? 0),
                        'lng' => (float)($ro->order->delivery_lng ?? 0),
                        'code' => $ro->order->order_code ?? '',
                    ])->filter(fn($s) => $s['lat'] && $s['lng'])->values(),
                    'total_distance' => $route->total_distance,
                    'estimated_time' => $route->estimated_time,
                ]
            ]);
        }

        return back()->with('success', "Đã tối ưu tuyến #{$routeId} bằng Nearest Neighbor! Tổng quãng đường: " . round($totalDist, 2) . " km.");
    }

    // ============================================================
    // 3. AUTO ASSIGN SHIPPER — Tự động phân công Shipper
    // ============================================================
    public function autoAssign(Request $request)
    {
        $request->validate([
            'route_ids'   => 'required|array|min:1',
            'route_ids.*' => 'exists:routes,id',
        ]);

        // Shipper đang online
        $shippers = Shipper::where('is_active', true)
            ->with(['user', 'orders' => function ($q) {
                $q->where('status', 'processing');
            }])
            ->whereNotNull('current_lat')
            ->whereNotNull('current_lng')
            ->get();

        // Fallback: lấy tất cả shipper online kể cả không có GPS
        if ($shippers->isEmpty()) {
            $shippers = Shipper::where('is_active', true)
                ->with(['user', 'orders' => function ($q) {
                    $q->where('status', 'processing');
                }])
                ->get();
        }

        if ($shippers->isEmpty()) {
            return back()->with('error', 'Không có Shipper nào đang trực tuyến (Online). Vui lòng bật trạng thái cho ít nhất 1 Shipper.');
        }

        $routes     = Route::with('routeOrders.order')->whereIn('id', $request->route_ids)->get();
        $assigned   = 0;
        $shipperLoadCount = $shippers->mapWithKeys(fn($s) => [$s->id => $s->orders->count()])->toArray();

        DB::transaction(function () use ($routes, $shippers, &$assigned, &$shipperLoadCount) {
            foreach ($routes as $route) {
                if ($route->shipper_id) continue; // đã gán rồi thì bỏ qua

                // Lấy tọa độ điểm lấy hàng đầu tiên của tuyến
                $firstStop   = $route->routeOrders->sortBy('stop_sequence')->first();
                $pickupLat   = $firstStop?->order?->pickup_lat ?? 21.028511;
                $pickupLng   = $firstStop?->order?->pickup_lng ?? 105.804817;

                // Tính heuristic score cho từng Shipper
                // score = khoảng cách đến điểm lấy hàng + số đơn đang chạy * 2 (km tương đương)
                $bestShipper = null;
                $bestScore   = PHP_INT_MAX;

                foreach ($shippers as $shipper) {
                    $shipperLat = (float) ($shipper->current_lat ?? 21.028511);
                    $shipperLng = (float) ($shipper->current_lng ?? 105.804817);

                    $dist  = $this->haversineKm($shipperLat, $shipperLng, (float)$pickupLat, (float)$pickupLng);
                    $load  = $shipperLoadCount[$shipper->id] ?? 0;
                    $score = $dist + ($load * 2.0); // mỗi đơn đang chạy "tương đương" 2 km xa hơn

                    if ($score < $bestScore) {
                        $bestScore   = $score;
                        $bestShipper = $shipper;
                    }
                }

                if (!$bestShipper) continue;

                // Gán Shipper vào Route
                $route->shipper_id = $bestShipper->id;
                $route->save();

                // Cập nhật tất cả đơn trong tuyến
                foreach ($route->routeOrders as $routeOrder) {
                    if ($routeOrder->order) {
                        $routeOrder->order->shipper_id        = $bestShipper->id;
                        $routeOrder->order->assignment_status = 'assigned';
                        $routeOrder->order->save();

                        // Ghi lịch sử phân công
                        ShipperAssignment::create([
                            'order_id'      => $routeOrder->order_id,
                            'shipper_id'    => $bestShipper->id,
                            'status'        => 'assigned',
                            'responded_at'  => null,
                        ]);

                        // Thông báo Shipper
                        if ($bestShipper->user) {
                            $bestShipper->user->notify(new \App\Notifications\OrderAssignedNotification($routeOrder->order));
                        }
                    }
                }

                // Tăng load count của shipper vừa được gán
                $shipperLoadCount[$bestShipper->id] = ($shipperLoadCount[$bestShipper->id] ?? 0) + $route->routeOrders->count();
                $assigned++;
            }
        });

        if ($assigned === 0) {
            return back()->with('info', 'Tất cả tuyến đã được gán Shipper rồi.');
        }

        return back()->with('success', "Đã tự động phân công {$assigned} tuyến cho Shipper (Heuristic: khoảng cách + tải đơn)!");
    }

    // ============================================================
    // API: Xem trước kết quả gom batch (AJAX preview)
    // ============================================================
    public function previewBatch(Request $request)
    {
        $radiusKm  = (float) ($request->radius_km ?? 5);
        $maxOrders = (int)  ($request->max_orders ?? 10);

        $orders = Order::where('assignment_status', 'unassigned')
            ->where('status', 'pending')
            ->whereNotNull('delivery_lat')
            ->whereNotNull('delivery_lng')
            ->with('customer.user')
            ->get();

        $batches  = [];
        $assigned = [];

        foreach ($orders as $order) {
            if (in_array($order->id, $assigned)) continue;
            $batch      = [$order->id];
            $assigned[] = $order->id;

            foreach ($orders as $other) {
                if (in_array($other->id, $assigned)) continue;
                if (count($batch) >= $maxOrders) break;

                $dist = $this->haversineKm(
                    (float)$order->delivery_lat, (float)$order->delivery_lng,
                    (float)$other->delivery_lat, (float)$other->delivery_lng
                );
                if ($dist <= $radiusKm) {
                    $batch[]    = $other->id;
                    $assigned[] = $other->id;
                }
            }
            $batches[] = ['center' => ['lat' => $order->delivery_lat, 'lng' => $order->delivery_lng], 'order_ids' => $batch, 'count' => count($batch)];
        }

        return response()->json([
            'batches'        => $batches,
            'total_orders'   => $orders->count(),
            'total_batches'  => count($batches),
            'orders_map'     => $orders->map(fn($o) => [
                'id'      => $o->id,
                'code'    => $o->order_code,
                'lat'     => (float)$o->delivery_lat,
                'lng'     => (float)$o->delivery_lng,
                'address' => $o->delivery_address,
            ])->values(),
        ]);
    }

    // ============================================================
    // 4. PHÂN TÍCH NÂNG CAO (ADVANCED ANALYTICS)
    // ============================================================

    public function getPriorityOrders(Request $request)
    {
        $lat = (float) $request->input('lat');
        $lng = (float) $request->input('lng');

        $orders = Order::where('assignment_status', 'unassigned')
            ->whereIn('status', ['pending'])
            ->with('customer.user')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($lat && $lng) {
            $orders = $orders->filter(function($o) use ($lat, $lng) {
                if (!$o->pickup_lat || !$o->pickup_lng) return false;
                $dist = $this->haversineKm($lat, $lng, (float)$o->pickup_lat, (float)$o->pickup_lng);
                return $dist < 50;
            });
        }

        $priorityData = $orders->map(function ($order) {
            $waitTimeMins = now()->diffInMinutes($order->created_at);
            $score = $waitTimeMins * 1.5;
            return [
                'id' => $order->id,
                'code' => $order->order_code,
                'address' => \Illuminate\Support\Str::limit($order->delivery_address, 30),
                'wait_time_mins' => $waitTimeMins,
                'priority_score' => round($score, 2),
                'created_at' => $order->created_at->format('d/m/Y H:i'),
            ];
        })->sortByDesc('priority_score')->take(10)->values();

        return response()->json(['success' => true, 'data' => $priorityData]);
    }

    public function compareShippers(Request $request)
    {
        $lat = (float) $request->input('lat', 10.7769);
        $lng = (float) $request->input('lng', 106.7009);

        $shippers = Shipper::where('is_active', true)
            ->with(['user', 'orders' => function ($q) {
                $q->where('status', 'processing');
            }])
            ->whereNotNull('current_lat')
            ->whereNotNull('current_lng')
            ->get();

        if ($shippers->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không có tài xế nào đang Online có tọa độ.']);
        }

        $results = [];
        foreach ($shippers as $shipper) {
            $dist = $this->haversineKm($lat, $lng, (float)$shipper->current_lat, (float)$shipper->current_lng);
            $load = $shipper->orders->count();
            
            // Công thức: 1 km tương đương 10 điểm phạt, 1 đơn đang chạy tương đương 15 điểm phạt
            // Điểm càng thấp (penalty thấp) càng tốt
            $optimalScore = ($dist * 10) + ($load * 15);

            $results[] = [
                'id' => $shipper->id,
                'name' => $shipper->user->username ?? 'Unknown',
                'distance_km' => round($dist, 2),
                'current_load' => $load,
                'score' => round($optimalScore, 2),
                'vehicle' => $shipper->vehicle_type,
            ];
        }

        usort($results, fn($a, $b) => $a['score'] <=> $b['score']);
        $top5 = array_slice($results, 0, 5);

        return response()->json(['success' => true, 'data' => $top5]);
    }

    public function getShipperCapacity()
    {
        $shippers = Shipper::with(['user', 'orders' => function ($q) {
            $q->where('status', 'processing');
        }])->get();

        $results = [];
        foreach ($shippers as $shipper) {
            $type = strtolower($shipper->vehicle_type ?? '');
            $maxCapacity = 10; // Mặc định xe máy: 10 đơn
            $maxWeight = 50; // Mặc định xe máy: 50 kg

            if (str_contains($type, 'truck') || str_contains($type, 'tải')) {
                $maxCapacity = 100;
                $maxWeight = 1500;
            } elseif (str_contains($type, 'van') || str_contains($type, 'bán tải')) {
                $maxCapacity = 30;
                $maxWeight = 500;
            }

            $currentLoad = $shipper->orders->count();
            $currentWeight = $shipper->orders->sum('total_weight');

            $remainingCap = max(0, $maxCapacity - $currentLoad);
            $remainingWeight = max(0, $maxWeight - $currentWeight);

            $results[] = [
                'id' => $shipper->id,
                'name' => $shipper->user->username ?? 'Unknown',
                'vehicle' => $shipper->vehicle_type ?? 'Xe máy',
                'max_orders' => $maxCapacity,
                'current_orders' => $currentLoad,
                'remaining_orders' => $remainingCap,
                'max_weight' => $maxWeight,
                'current_weight' => round($currentWeight, 2),
                'remaining_weight' => round($remainingWeight, 2),
                'status' => $shipper->is_active ? 'Online' : 'Offline',
                'overloaded' => $currentLoad >= $maxCapacity || $currentWeight >= $maxWeight
            ];
        }

        return response()->json(['success' => true, 'data' => $results]);
    }

    // ============================================================
    // PRIVATE HELPERS
    // ============================================================

    /**
     * Haversine formula — tính khoảng cách km giữa 2 tọa độ GPS
     */
    private function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R   = 6371; // Bán kính Trái Đất (km)
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a   = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Nearest Neighbor Algorithm — tối ưu thứ tự điểm giao
     * Bắt đầu từ điểm đầu tiên, luôn đi đến điểm gần nhất chưa thăm
     */
    private function nearestNeighborSort(array $orders): array
    {
        if (count($orders) <= 1) return $orders;

        $remaining = $orders;
        $sorted    = [array_shift($remaining)];

        while (!empty($remaining)) {
            $last     = end($sorted);
            $bestIdx  = 0;
            $bestDist = PHP_FLOAT_MAX;

            foreach ($remaining as $idx => $candidate) {
                $dist = $this->haversineKm(
                    (float)$last->delivery_lat, (float)$last->delivery_lng,
                    (float)$candidate->delivery_lat, (float)$candidate->delivery_lng
                );
                if ($dist < $bestDist) {
                    $bestDist = $dist;
                    $bestIdx  = $idx;
                }
            }

            $sorted[]  = $remaining[$bestIdx];
            array_splice($remaining, $bestIdx, 1);
        }

        return $sorted;
    }

    /**
     * Tính tổng quãng đường (km) của một chuỗi điểm giao hàng
     */
    private function calcTotalDistance(array $orders): float
    {
        $total = 0.0;
        for ($i = 0; $i < count($orders) - 1; $i++) {
            $total += $this->haversineKm(
                (float)$orders[$i]->delivery_lat,  (float)$orders[$i]->delivery_lng,
                (float)$orders[$i+1]->delivery_lat, (float)$orders[$i+1]->delivery_lng
            );
        }
        return $total;
    }
}
