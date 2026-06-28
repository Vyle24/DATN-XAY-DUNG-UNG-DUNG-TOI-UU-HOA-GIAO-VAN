@extends('layouts.customer')

@section('header_title', 'Đặt Đơn Hàng Mới')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Left form block -->
    <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/60 shadow-sm p-5 md:p-6 flex flex-col justify-between">
        <form action="{{ route('customer.orders.store') }}" method="POST" id="orderForm">
            @csrf
            
            <h4 class="font-bold text-slate-800 text-sm mb-4 pb-2 border-b border-slate-100 flex items-center">
                <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                Thông tin lấy & giao nhận
            </h4>

            <div class="space-y-4">
                <!-- Select Hub -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">1. Điểm gửi hàng (Chọn Bưu cục/Trạm)</label>
                    <select name="hub_id" id="hub_id" class="w-full rounded-xl border-slate-250/80 bg-slate-50/50 px-3 py-2 text-xs font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" required>
                        <option value="">-- Chọn Bưu cục gần bạn nhất --</option>
                        @foreach($hubs as $hub)
                        <option value="{{ $hub->id }}" 
                                data-address="{{ $hub->address }}" 
                                data-lat="{{ $hub->latitude }}" 
                                data-lng="{{ $hub->longitude }}">
                            {{ $hub->name }} ({{ $hub->address }})
                        </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="pickup_address" id="pickup_address">
                    <input type="hidden" name="pickup_lat" id="pickup_lat">
                    <input type="hidden" name="pickup_lng" id="pickup_lng">
                </div>

                <!-- Delivery Address (API selection) -->
                <div class="space-y-3 bg-slate-50/50 border border-slate-150 p-4 rounded-2xl">
                    <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">2. Địa chỉ nhận hàng</label>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Tỉnh / Thành</label>
                            <select id="select_province" class="w-full rounded-xl border-slate-200 bg-white px-2 py-1.5 text-[11px] font-semibold focus:ring-1 focus:ring-blue-500">
                                <option value="">Đang tải...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Quận / Huyện</label>
                            <select id="select_district" class="w-full rounded-xl border-slate-200 bg-white px-2 py-1.5 text-[11px] font-semibold focus:ring-1 focus:ring-blue-500">
                                <option value="">-- Chọn Quận/Huyện --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Phường / Xã</label>
                            <select id="select_ward" class="w-full rounded-xl border-slate-200 bg-white px-2 py-1.5 text-[11px] font-semibold focus:ring-1 focus:ring-blue-500">
                                <option value="">-- Chọn Phường/Xã --</option>
                            </select>
                        </div>
                    </div>

                    <div class="relative">
                        <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Địa chỉ chi tiết (Số nhà, ngõ, tên đường...)</label>
                        <input type="text" id="address_detail" autocomplete="off" placeholder="Ví dụ: Số 15 ngách 2 ngõ 1..." class="w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-xs font-semibold focus:ring-1 focus:ring-blue-500">
                        <ul id="address_suggestions" class="absolute z-50 w-full bg-white border border-slate-200 rounded-xl shadow-2xl mt-1 hidden max-h-48 overflow-y-auto text-xs divide-y divide-slate-100"></ul>
                    </div>

                    <div>
                        <button type="button" onclick="geocodeAddress()" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-500/10 transition flex items-center justify-center gap-1.5">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            📍 Cập nhật vị trí lên bản đồ
                        </button>
                    </div>

                    <!-- Hidden input to hold full delivery address submitted in form -->
                    <input type="hidden" name="delivery_address" id="delivery_address">
                </div>

                <!-- Select District Rate -->
                <!-- Select District Rate (Hidden to avoid confusion) -->
                <div class="hidden">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">3. Khu vực giao hàng (Tính phí)</label>
                    <select id="district_rate" name="district_rate_id" class="w-full rounded-xl border-slate-250/80 bg-slate-50/50 px-3 py-2 text-xs font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" required>
                        <option value="">-- Chọn Quận/Huyện giao hàng --</option>
                        @foreach($rates as $rate)
                        <option value="{{ $rate->id }}" 
                                data-base="{{ $rate->base_price }}" 
                                data-perkm="{{ $rate->price_per_km }}">
                            {{ $rate->district_name }} (Cơ bản: {{ number_format($rate->base_price) }}đ + {{ number_format($rate->price_per_km) }}đ/km)
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Hidden Lat / Lng inputs -->
                <input type="hidden" name="delivery_lat" id="delivery_lat" required>
                <input type="hidden" name="delivery_lng" id="delivery_lng" required>

                <div class="border-t border-slate-100 my-4 pt-3"></div>

                <h4 class="font-bold text-slate-800 text-sm mb-4 flex items-center">
                    <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    Chi tiết kiện hàng & Thanh toán
                </h4>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Trọng lượng (kg)</label>
                        <input type="number" step="0.01" name="total_weight" placeholder="0.5" class="w-full rounded-xl border-slate-250/80 bg-slate-50/50 px-3 py-2 text-xs font-semibold focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Thanh toán</label>
                        <select name="payment_method" class="w-full rounded-xl border-slate-250/80 bg-slate-50/50 px-3 py-2 text-xs font-semibold focus:border-blue-500" required>
                            <option value="COD">Thu hộ (COD)</option>
                            <option value="Prepaid">Trả trước</option>
                        </select>
                    </div>
                </div>

                <!-- Voucher apply -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Áp dụng Voucher</label>
                    <div class="flex gap-2">
                        <input type="text" id="voucher_code" placeholder="Mã giảm giá..." class="flex-1 rounded-xl border-slate-250/80 bg-slate-50/50 px-3 py-2 text-xs font-semibold focus:border-blue-500 uppercase">
                        <button type="button" onclick="verifyVoucher()" class="bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs px-4 rounded-xl transition">Áp dụng</button>
                    </div>
                    <span id="voucher_msg" class="text-[10px] font-medium mt-1 block"></span>
                    <input type="hidden" name="applied_voucher_code" id="applied_voucher_code">
                </div>
            </div>

            <!-- Price Breakdown Box -->
            <div class="bg-slate-50 border border-slate-200/60 rounded-2xl p-4 mt-6 space-y-2.5">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-450 font-medium">Khoảng cách giao nhận:</span>
                    <span class="font-bold text-slate-700" id="info_distance">0.00 km</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-450 font-medium">Phí tạm tính:</span>
                    <span class="font-bold text-slate-700" id="info_subtotal">0đ</span>
                </div>
                <div class="flex justify-between items-center text-xs text-rose-600">
                    <span class="font-medium">Voucher giảm giá:</span>
                    <span class="font-bold" id="info_discount">-0đ</span>
                </div>
                <div class="border-t border-slate-200/60 my-2 pt-2 flex justify-between items-center text-sm">
                    <span class="font-bold text-slate-800">Tổng phí cuối cùng:</span>
                    <span class="font-extrabold text-blue-600 text-base" id="info_total">0đ</span>
                </div>
            </div>
            
            <input type="hidden" name="shipping_fee" id="shipping_fee" value="0">

            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs py-3 rounded-2xl shadow-lg shadow-blue-500/15 mt-6 transition">
                Xác nhận đặt đơn hàng
            </button>
        </form>
    </div>

    <!-- Right Map Block -->
    <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/60 shadow-sm p-4 flex flex-col h-[400px] lg:h-[650px]">
        <div class="mb-2 flex items-center justify-between">
            <div>
                <h4 class="font-bold text-slate-800 text-sm">Bản Đồ Ghim Vị Trí Giao Hàng</h4>
                <p class="text-[10px] text-slate-400 font-medium">Kéo thả ghim đỏ hoặc nhấp chuột để chọn vị trí chính xác</p>
            </div>
            <div id="map_status" class="text-[9px] font-bold px-2 py-0.5 rounded bg-amber-50 text-amber-600 border border-amber-100 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                Chưa có điểm giao
            </div>
        </div>
        <div id="orderMap" class="flex-1 w-full rounded-xl overflow-hidden border border-slate-200/60"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Custom beautiful toast notification system
    function showToast(message, type = 'success') {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed top-5 right-5 z-[9999] space-y-3 pointer-events-none max-w-sm w-full px-4 sm:px-0';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto flex items-center p-4 rounded-2xl shadow-xl border transform translate-x-full opacity-0 transition-all duration-300 ease-out';
        
        let bg = 'bg-white border-slate-100 text-slate-800';
        let iconHtml = '';
        if (type === 'success') {
            bg = 'bg-emerald-50 border-emerald-100 text-emerald-800';
            iconHtml = `<svg class="h-5 w-5 text-emerald-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        } else if (type === 'error') {
            bg = 'bg-rose-50 border-rose-100 text-rose-800';
            iconHtml = `<svg class="h-5 w-5 text-rose-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        } else if (type === 'warning') {
            bg = 'bg-amber-50 border-amber-100 text-amber-800';
            iconHtml = `<svg class="h-5 w-5 text-amber-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
        }

        toast.className += ` ${bg}`;
        toast.innerHTML = `
            ${iconHtml}
            <div class="text-xs font-bold">${message}</div>
            <button type="button" onclick="this.parentElement.remove()" class="ml-auto text-slate-400 hover:text-slate-600 transition pl-3">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        `;

        container.appendChild(toast);

        // Animation trigger
        setTimeout(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        }, 10);

        // Auto remove
        setTimeout(() => {
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 4000);
    }

    let map;
    let pickupMarker = null;
    let deliveryMarker = null;
    
    let pickupLat = 0;
    let pickupLng = 0;
    let deliveryLat = 0;
    let deliveryLng = 0;

    let baseRate = 0;
    let pricePerKm = 0;
    let discountPercent = 0;
    let maxDiscount = 0;

    // Global pricing settings from backend
    const baseWeightLimit = {{ $baseWeightLimit }};
    const pricePerKg = {{ $pricePerKg }};

    // Helper to clean district names for matching
    function cleanDistrictName(name) {
        if (!name) return "";
        return name.toLowerCase()
            .replace(/^(quận|huyện|thị xã|thành phố)\s+/i, '')
            .replace(/\(.*\)/, '') // Remove pricing details in option text
            .trim();
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Init map centered on Hanoi
        map = L.map('orderMap').setView([21.028511, 105.804817], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Click on map to set delivery location
        map.on('click', function(e) {
            setDeliveryLocation(e.latlng.lat, e.latlng.lng);
            reverseGeocode(e.latlng.lat, e.latlng.lng);
        });

        // Load Provinces from API
        const provinceSelect = document.getElementById('select_province');
        const districtSelect = document.getElementById('select_district');
        const wardSelect = document.getElementById('select_ward');

        fetch('https://provinces.open-api.vn/api/p/')
            .then(res => res.json())
            .then(provinces => {
                provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành --</option>';
                provinces.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.code;
                    opt.text = p.name;
                    // Default select Hanoi (code 1)
                    if (p.name.includes("Hà Nội") || p.code == 1) {
                        opt.selected = true;
                    }
                    provinceSelect.appendChild(opt);
                });
                
                // Trigger change to load districts for Hanoi
                provinceSelect.dispatchEvent(new Event('change'));
            })
            .catch(err => {
                console.error("Error loading provinces:", err);
                provinceSelect.innerHTML = '<option value="1">Thành phố Hà Nội</option>';
                provinceSelect.dispatchEvent(new Event('change'));
            });

        // Province change listener
        provinceSelect.addEventListener('change', function() {
            const pCode = this.value;
            if (!pCode) {
                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                return;
            }
            
            districtSelect.innerHTML = '<option value="">Đang tải...</option>';
            fetch(`https://provinces.open-api.vn/api/p/${pCode}?depth=2`)
                .then(res => res.json())
                .then(data => {
                    districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                    if (data.districts) {
                        data.districts.forEach(d => {
                            const opt = document.createElement('option');
                            opt.value = d.code;
                            opt.text = d.name;
                            districtSelect.appendChild(opt);
                        });
                    }
                })
                .catch(err => {
                    console.error("Error loading districts:", err);
                    districtSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                });
        });

        // District change listener
        districtSelect.addEventListener('change', function() {
            const dCode = this.value;
            if (!dCode) {
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                return;
            }
            
            wardSelect.innerHTML = '<option value="">Đang tải...</option>';
            fetch(`https://provinces.open-api.vn/api/d/${dCode}?depth=2`)
                .then(res => res.json())
                .then(data => {
                    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                    if (data.wards) {
                        data.wards.forEach(w => {
                            const opt = document.createElement('option');
                            opt.value = w.code;
                            opt.text = w.name;
                            wardSelect.appendChild(opt);
                        });
                    }
                })
                .catch(err => {
                    console.error("Error loading wards:", err);
                    wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                });

            // Auto-select corresponding shipping rate in #district_rate
            const selectedDistrictName = this.options[this.selectedIndex]?.text;
            if (selectedDistrictName) {
                const rateSelect = document.getElementById('district_rate');
                let matched = false;
                for (let i = 0; i < rateSelect.options.length; i++) {
                    const optText = rateSelect.options[i].text;
                    if (cleanDistrictName(optText).includes(cleanDistrictName(selectedDistrictName)) ||
                        cleanDistrictName(selectedDistrictName).includes(cleanDistrictName(optText))) {
                        rateSelect.selectedIndex = i;
                        rateSelect.dispatchEvent(new Event('change'));
                        matched = true;
                        break;
                    }
                }
            }
        });

        // Ward change listener (to bound map)
        wardSelect.addEventListener('change', function() {
            const province = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
            const district = districtSelect.options[districtSelect.selectedIndex]?.text || '';
            const ward = this.options[this.selectedIndex]?.text || '';
            
            if (ward && district && province) {
                const query = encodeURIComponent(ward + ', ' + district + ', ' + province);
                
                const statusSpan = document.getElementById('map_status');
                statusSpan.className = "text-[9px] font-bold px-2 py-0.5 rounded bg-amber-50 text-amber-600 border border-amber-100 flex items-center gap-1";
                statusSpan.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>Đang quét khu vực...';

                fetch(`https://nominatim.openstreetmap.org/search?q=${query}&format=json&limit=1`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) {
                            // Focus map on ward and place initial marker
                            const lat = parseFloat(data[0].lat);
                            const lon = parseFloat(data[0].lon);
                            
                            // Restrict map bounds
                            if (data[0].boundingbox) {
                                const bbox = data[0].boundingbox;
                                const bounds = [
                                    [parseFloat(bbox[0]), parseFloat(bbox[2])], // SouthWest
                                    [parseFloat(bbox[1]), parseFloat(bbox[3])]  // NorthEast
                                ];
                                map.fitBounds(bounds);
                                map.setMaxBounds(bounds); // This effectively locks the map area
                            } else {
                                map.setView([lat, lon], 15);
                            }
                            
                            setDeliveryLocation(lat, lon);
                            document.getElementById('delivery_address').value = ward + ', ' + district + ', ' + province;
                            showToast(`Đã khoanh vùng khu vực ${ward}. Hãy gõ số nhà hoặc ghim trên bản đồ.`, 'success');
                        }
                    });
            } else {
                // If unset, clear maxBounds
                map.setMaxBounds(null);
            }
        });

        // Hub change listener
        document.getElementById('hub_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (!selectedOption.value) {
                if (pickupMarker) {
                    map.removeLayer(pickupMarker);
                    pickupMarker = null;
                }
                pickupLat = 0;
                pickupLng = 0;
                updatePricing();
                return;
            }

            pickupLat = parseFloat(selectedOption.dataset.lat);
            pickupLng = parseFloat(selectedOption.dataset.lng);
            const address = selectedOption.dataset.address;

            document.getElementById('pickup_address').value = address;
            document.getElementById('pickup_lat').value = pickupLat;
            document.getElementById('pickup_lng').value = pickupLng;

            // Draw or move pickup marker
            if (pickupMarker) {
                pickupMarker.setLatLng([pickupLat, pickupLng]);
            } else {
                pickupMarker = L.marker([pickupLat, pickupLng], {
                    icon: L.divIcon({
                        className: '',
                        html: `<div style="background:#ef4444;border:2px solid white;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 6px rgba(0,0,0,0.3);position:relative;">
                                   <svg style="width:16px;height:16px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                   <div style="position:absolute;bottom:-6px;left:50%;transform:translateX(-50%);border-left:5px solid transparent;border-right:5px solid transparent;border-top:7px solid #ef4444;"></div>
                               </div>`,
                        iconSize: [32, 40],
                        iconAnchor: [16, 40]
                    })
                }).addTo(map).bindPopup("<b>Điểm lấy hàng (Hub)</b>").openPopup();
            }

            map.panTo([pickupLat, pickupLng]);
            updatePricing();
        });

        // District rate change listener
        document.getElementById('district_rate').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (!selectedOption.value) {
                baseRate = 0;
                pricePerKm = 0;
            } else {
                baseRate = parseFloat(selectedOption.dataset.base);
                pricePerKm = parseFloat(selectedOption.dataset.perkm);
            }
            updatePricing();
        });

        // Weight change listener
        const weightInput = document.querySelector('input[name="total_weight"]');
        if (weightInput) {
            weightInput.addEventListener('input', updatePricing);
            weightInput.addEventListener('change', updatePricing);
        }

        // Detail address Autocomplete
        let debounceTimer;
        const detailInput = document.getElementById('address_detail');
        const suggestionsBox = document.getElementById('address_suggestions');
        
        detailInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const val = this.value.trim();
            
            if (val.length < 3) {
                suggestionsBox.classList.add('hidden');
                return;
            }
            
            debounceTimer = setTimeout(() => {
                const province = document.getElementById('select_province').options[document.getElementById('select_province').selectedIndex]?.text || '';
                const district = document.getElementById('select_district').options[document.getElementById('select_district').selectedIndex]?.text || '';
                const ward = document.getElementById('select_ward').options[document.getElementById('select_ward').selectedIndex]?.text || '';
                
                if (!province || !district) return; // Require at least district
                
                // Add bounded parameter to limit search to the map's current bounding box!
                const boundsStr = map.getBounds() ? `&viewbox=${map.getBounds().getWest()},${map.getBounds().getSouth()},${map.getBounds().getEast()},${map.getBounds().getNorth()}&bounded=1` : '';
                const query = encodeURIComponent(val + ', ' + (ward ? ward + ', ' : '') + district + ', ' + province);
                const url = `https://nominatim.openstreetmap.org/search?q=${query}&format=json&limit=5&addressdetails=1${boundsStr}`;
                
                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        suggestionsBox.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                const li = document.createElement('li');
                                li.className = 'px-3 py-2.5 hover:bg-blue-50 cursor-pointer transition-colors';
                                
                                const namePart = item.name ? `<span class="font-bold text-slate-700">${item.name}</span>` : '';
                                const descPart = `<span class="text-slate-500">${item.display_name}</span>`;
                                li.innerHTML = namePart ? namePart + '<br>' + descPart : descPart;
                                
                                li.onclick = () => {
                                    detailInput.value = item.name || val;
                                    suggestionsBox.classList.add('hidden');
                                    
                                    const lat = parseFloat(item.lat);
                                    const lon = parseFloat(item.lon);
                                    setDeliveryLocation(lat, lon);
                                    map.setView([lat, lon], 18);
                                    document.getElementById('delivery_address').value = item.display_name;
                                    showToast('Đã chốt vị trí!', 'success');
                                };
                                suggestionsBox.appendChild(li);
                            });
                            suggestionsBox.classList.remove('hidden');
                        } else {
                            suggestionsBox.classList.add('hidden');
                        }
                    })
                    .catch(err => console.error(err));
            }, 600); // Debounce typing
        });

        // Hide autocomplete when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.id !== 'address_detail' && !suggestionsBox.contains(e.target)) {
                suggestionsBox.classList.add('hidden');
            }
        });

    });

    function setDeliveryLocation(lat, lng) {
        deliveryLat = lat;
        deliveryLng = lng;

        document.getElementById('delivery_lat').value = lat.toFixed(6);
        document.getElementById('delivery_lng').value = lng.toFixed(6);

        if (deliveryMarker) {
            deliveryMarker.setLatLng([lat, lng]);
        } else {
            deliveryMarker = L.marker([lat, lng], {
                draggable: true,
                icon: L.divIcon({
                    className: '',
                    html: `<div style="background:#10b981;border:2px solid white;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 6px rgba(0,0,0,0.3);position:relative;">
                               <svg style="width:16px;height:16px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                               <div style="position:absolute;bottom:-6px;left:50%;transform:translateX(-50%);border-left:5px solid transparent;border-right:5px solid transparent;border-top:7px solid #10b981;"></div>
                           </div>`,
                    iconSize: [32, 40],
                    iconAnchor: [16, 40]
                })
            }).addTo(map).bindPopup("<b>Điểm giao hàng</b>").openPopup();

            deliveryMarker.on('dragend', function(e) {
                const position = deliveryMarker.getLatLng();
                setDeliveryLocation(position.lat, position.lng);
                reverseGeocode(position.lat, position.lng);
            });
        }

        const mapStatus = document.getElementById('map_status');
        mapStatus.className = "text-[9px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center gap-1";
        mapStatus.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Đã chọn vị trí';

        updatePricing();
    }

    function geocodeAddress() {
        const provinceSelect = document.getElementById('select_province');
        const districtSelect = document.getElementById('select_district');
        const wardSelect = document.getElementById('select_ward');
        const detailInput = document.getElementById('address_detail');

        const province = provinceSelect.options[provinceSelect.selectedIndex]?.value ? provinceSelect.options[provinceSelect.selectedIndex].text : '';
        const district = districtSelect.options[districtSelect.selectedIndex]?.value ? districtSelect.options[districtSelect.selectedIndex].text : '';
        const ward = wardSelect.options[wardSelect.selectedIndex]?.value ? wardSelect.options[wardSelect.selectedIndex].text : '';
        const detail = detailInput.value.trim();

        if (!province || !district || !ward) {
            showToast('Vui lòng chọn đầy đủ Tỉnh/Thành, Quận/Huyện, Phường/Xã!', 'warning');
            return;
        }

        // Construct full address string
        const fullAddress = (detail ? detail + ', ' : '') + ward + ', ' + district + ', ' + province;
        document.getElementById('delivery_address').value = fullAddress;

        // Call Nominatim API for geocoding
        const query = encodeURIComponent(fullAddress);
        const url = `https://nominatim.openstreetmap.org/search?q=${query}&format=json&limit=1`;

        // Show loading state
        const statusSpan = document.getElementById('map_status');
        statusSpan.className = "text-[9px] font-bold px-2 py-0.5 rounded bg-amber-50 text-amber-600 border border-amber-100 flex items-center gap-1";
        statusSpan.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>Đang tìm vị trí...';

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    
                    // Update Lat Lng input fields
                    setDeliveryLocation(lat, lon);
                    
                    // Center and zoom map to the location
                    map.setView([lat, lon], 16);
                    showToast('Đã tìm thấy vị trí chính xác trên bản đồ!', 'success');
                } else if (detail) {
                    // Try fallback without detail address (just Ward, District, Province)
                    const fallbackAddress = ward + ', ' + district + ', ' + province;
                    const fallbackUrl = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(fallbackAddress)}&format=json&limit=1`;
                    
                    fetch(fallbackUrl)
                        .then(res2 => res2.json())
                        .then(data2 => {
                            if (data2 && data2.length > 0) {
                                const lat = parseFloat(data2[0].lat);
                                const lon = parseFloat(data2[0].lon);
                                setDeliveryLocation(lat, lon);
                                map.setView([lat, lon], 14);
                                showToast('Không tìm thấy số nhà chi tiết. Đã định vị về trung tâm ' + ward + '. Bạn có thể kéo thả ghim đỏ để chọn vị trí chính xác.', 'warning');
                            } else {
                                showToast('Không tìm thấy vị trí cho địa chỉ này. Vui lòng tự ghim trực tiếp trên bản đồ.', 'error');
                                statusSpan.className = "text-[9px] font-bold px-2 py-0.5 rounded bg-rose-50 text-rose-600 border border-rose-100 flex items-center gap-1";
                                statusSpan.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Lỗi tìm vị trí';
                            }
                        })
                        .catch(() => {
                            showToast('Lỗi kết nối bản đồ. Vui lòng tự ghim trực tiếp trên bản đồ.', 'error');
                            statusSpan.className = "text-[9px] font-bold px-2 py-0.5 rounded bg-rose-50 text-rose-600 border border-rose-100 flex items-center gap-1";
                            statusSpan.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Lỗi kết nối';
                        });
                } else {
                    showToast('Không tìm thấy vị trí cho địa chỉ này. Vui lòng tự ghim trực tiếp trên bản đồ.', 'error');
                    statusSpan.className = "text-[9px] font-bold px-2 py-0.5 rounded bg-rose-50 text-rose-600 border border-rose-100 flex items-center gap-1";
                    statusSpan.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Lỗi tìm vị trí';
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Lỗi kết nối dịch vụ bản đồ. Bạn có thể tự ghim trực tiếp trên bản đồ.', 'error');
                statusSpan.className = "text-[9px] font-bold px-2 py-0.5 rounded bg-rose-50 text-rose-600 border border-rose-100 flex items-center gap-1";
                statusSpan.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Lỗi kết nối';
            });
    }

    function reverseGeocode(lat, lng) {
        const url = `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&accept-language=vi`;
        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data && data.display_name) {
                    const cleanAddress = data.display_name;
                    document.getElementById('delivery_address').value = cleanAddress;
                    
                    // Update detail input
                    document.getElementById('address_detail').value = cleanAddress;
                    
                    // Attempt to parse district from Nominatim address object
                    if (data.address) {
                        const districtCandidates = [
                            data.address.suburb,
                            data.address.quarter,
                            data.address.city_district,
                            data.address.district,
                            data.address.town,
                            data.address.county
                        ];
                        
                        let foundDistrict = "";
                        for (let d of districtCandidates) {
                            if (d) {
                                foundDistrict = d;
                                break;
                            }
                        }
                        
                        if (foundDistrict) {
                            const rateSelect = document.getElementById('district_rate');
                            for (let i = 0; i < rateSelect.options.length; i++) {
                                const optText = rateSelect.options[i].text;
                                if (cleanDistrictName(optText).includes(cleanDistrictName(foundDistrict)) ||
                                    cleanDistrictName(foundDistrict).includes(cleanDistrictName(optText))) {
                                    rateSelect.selectedIndex = i;
                                    rateSelect.dispatchEvent(new Event('change'));
                                    break;
                                }
                            }
                        }
                    }
                }
            })
            .catch(err => console.error("Reverse geocoding error:", err));
    }

    // Haversine distance
    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    function updatePricing() {
        if (!pickupLat || !pickupLng || !deliveryLat || !deliveryLng || !baseRate) {
            document.getElementById('info_distance').innerText = "0.00 km";
            document.getElementById('info_subtotal').innerText = "0đ";
            document.getElementById('info_discount').innerText = "-0đ";
            document.getElementById('info_total').innerText = "0đ";
            document.getElementById('shipping_fee').value = 0;
            return;
        }

        const distance = getDistance(pickupLat, pickupLng, deliveryLat, deliveryLng);
        let subtotal = baseRate + (distance * pricePerKm);
        
        // Add weight pricing
        const weightInputVal = document.querySelector('input[name="total_weight"]').value;
        const weight = weightInputVal ? parseFloat(weightInputVal) : 0;
        if (weight > baseWeightLimit) {
            const extraWeight = weight - baseWeightLimit;
            subtotal += extraWeight * pricePerKg;
        }

        let discount = 0;
        if (discountPercent > 0) {
            discount = (subtotal * discountPercent) / 100;
            if (maxDiscount > 0 && discount > maxDiscount) {
                discount = maxDiscount;
            }
        }

        const total = Math.max(subtotal - discount, 0);

        document.getElementById('info_distance').innerText = distance.toFixed(2) + " km";
        document.getElementById('info_subtotal').innerText = Math.round(subtotal).toLocaleString() + "đ";
        document.getElementById('info_discount').innerText = "-" + Math.round(discount).toLocaleString() + "đ";
        document.getElementById('info_total').innerText = Math.round(total).toLocaleString() + "đ";
        
        document.getElementById('shipping_fee').value = Math.round(total);
    }

    function verifyVoucher() {
        const code = document.getElementById('voucher_code').value.trim();
        const msgSpan = document.getElementById('voucher_msg');
        
        if (!code) {
            msgSpan.className = "text-[10px] font-medium mt-1 block text-slate-400";
            msgSpan.innerText = "Vui lòng nhập mã giảm giá.";
            return;
        }

        fetch("{{ route('customer.vouchers.apply') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: code })
        })
        .then(async response => {
            const data = await response.json();
            if (response.ok && data.success) {
                discountPercent = data.discount_percent;
                maxDiscount = data.max_discount;
                document.getElementById('applied_voucher_code').value = data.code;
                
                msgSpan.className = "text-[10px] font-bold mt-1 block text-emerald-600";
                msgSpan.innerText = data.message;
                updatePricing();
            } else {
                discountPercent = 0;
                maxDiscount = 0;
                document.getElementById('applied_voucher_code').value = "";
                
                msgSpan.className = "text-[10px] font-bold mt-1 block text-rose-600";
                msgSpan.innerText = data.message || "Không áp dụng được mã.";
                updatePricing();
            }
        })
        .catch(err => {
            discountPercent = 0;
            maxDiscount = 0;
            document.getElementById('applied_voucher_code').value = "";
            
            msgSpan.className = "text-[10px] font-bold mt-1 block text-rose-600";
            msgSpan.innerText = "Có lỗi xảy ra khi xác thực voucher.";
            updatePricing();
        });
    }
</script>
@endsection
