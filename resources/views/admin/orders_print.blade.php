<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Vận Đơn #{{ $order->order_code }}</title>
    <!-- Use Tailwind via CDN for easy styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background-color: white !important; margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .print-container {
                width: 100%;
                max-width: 10cm; /* Standard A6 width for thermal printers */
                margin: 0 auto;
                padding: 10px;
                border: 1px solid #000;
                page-break-after: always;
            }
            .dashed-line {
                border-top: 1px dashed #000;
                margin: 8px 0;
            }
        }
        /* Default view styling (web preview) */
        body { background-color: #f1f5f9; padding: 20px; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        .print-container {
            width: 10cm; /* ~378px */
            background: white;
            margin: 0 auto;
            padding: 15px;
            border: 2px solid #1e293b;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        .dashed-line { border-top: 1px dashed #475569; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="no-print flex justify-center gap-4 mb-6">
        <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg shadow hover:bg-blue-700">🖨️ In Vận Đơn Ngay</button>
        <button onclick="window.close()" class="px-6 py-2 bg-slate-200 text-slate-700 font-bold rounded-lg shadow hover:bg-slate-300">Đóng</button>
    </div>

    <div class="print-container text-xs text-slate-800">
        <!-- Header -->
        <div class="flex justify-between items-start mb-2">
            <div>
                <h1 class="text-base font-black tracking-tight">LOGISTICS HUB</h1>
                <p class="text-[9px] text-slate-500 font-semibold">Tối Ưu Tuyến Đường Giao Nhận</p>
            </div>
            <div id="qrcode" class="h-12 w-12 flex-shrink-0 border border-slate-200 p-0.5"></div>
        </div>

        <div class="dashed-line"></div>

        <!-- Sender / Receiver -->
        <div class="space-y-2.5">
            <div>
                <div class="text-[10px] font-bold uppercase">Người gửi (Hub)</div>
                <div class="font-bold text-sm">{{ $order->hub->name ?? 'Hub Trung Tâm' }}</div>
                <div class="text-[11px] leading-snug">{{ $order->pickup_address }}</div>
            </div>

            <div>
                <div class="text-[10px] font-bold uppercase mt-1">Người nhận</div>
                <div class="font-bold text-sm">{{ $order->customer->user->username ?? 'Khách hàng' }} - {{ $order->customer->user->phone ?? 'N/A' }}</div>
                <div class="text-[11px] font-medium leading-snug break-words">{{ $order->delivery_address }}</div>
            </div>
        </div>

        <div class="dashed-line"></div>

        <!-- Details -->
        <div class="grid grid-cols-2 gap-2 text-[11px]">
            <div>
                <span class="text-[9px] font-bold uppercase block text-slate-500">Khối lượng</span>
                <span class="font-bold">{{ $order->total_weight }} kg</span>
            </div>
            <div>
                <span class="text-[9px] font-bold uppercase block text-slate-500">Thanh toán</span>
                <span class="font-bold uppercase">{{ $order->payment_method }}</span>
            </div>
            <div class="col-span-2">
                <span class="text-[9px] font-bold uppercase block text-slate-500">Trạng thái (Lúc in)</span>
                <span class="font-bold uppercase">{{ $order->status }}</span>
            </div>
        </div>

        <div class="dashed-line"></div>

        <!-- COD & Shipping Fee -->
        <div class="text-center py-2 bg-slate-100/50 rounded-lg border border-slate-200">
            <div class="text-[10px] font-bold uppercase text-slate-500">Thu Tiền Mặt (COD) / Phí Vận Chuyển</div>
            <div class="text-2xl font-black tracking-tighter mt-1">{{ number_format($order->shipping_fee) }} <span class="text-sm">VND</span></div>
            @if($order->payment_method == 'Prepaid' || $order->payment_status == 'paid')
                <div class="text-[10px] font-black text-emerald-600 mt-1">(ĐÃ THANH TOÁN TRƯỚC)</div>
            @else
                <div class="text-[10px] font-bold text-rose-600 mt-1">(SHIPPER THU KHI GIAO)</div>
            @endif
        </div>

        <div class="dashed-line"></div>

        <!-- Barcode -->
        <div class="flex flex-col items-center mt-3">
            <svg id="barcode" class="w-full h-16"></svg>
            <div class="text-[9px] text-slate-400 mt-1">Ngày tạo: {{ $order->created_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <!-- Scripts for Barcode & QR Code -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Generate Barcode (Code128)
            JsBarcode("#barcode", "{{ $order->order_code }}", {
                format: "CODE128",
                width: 2,
                height: 40,
                displayValue: true,
                fontSize: 14,
                fontOptions: "bold",
                margin: 0
            });

            // Generate QR Code (tracking url or simple order code)
            const qrUrl = "{{ url('/') }}/track?code={{ $order->order_code }}";
            new QRCode(document.getElementById("qrcode"), {
                text: qrUrl,
                width: 48,
                height: 48,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.L
            });
        });
    </script>
</body>
</html>
