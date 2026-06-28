@extends('layouts.admin')

@section('header_title', 'Quản Lý Điểm Điều Phối (Hubs)')

@section('content')

<!-- Split Page Grid: Table & Stats on Left, Map Sidebar on Right -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Left Column: Stats + Search + Table (2/3 width on desktop) -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tổng số trạm Hub</span>
                <span class="text-xl font-extrabold text-slate-800 mt-1 block">{{ $stats['total'] }}</span>
                <span class="text-[10px] text-slate-400 mt-1 block">Hoạt động kết nối</span>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Đơn hàng qua trạm</span>
                <span class="text-xl font-extrabold text-blue-600 mt-1 block">{{ $stats['total_orders'] }}</span>
                <span class="text-[10px] text-slate-400 mt-1 block">Trung chuyển tích lũy</span>
            </div>
        </div>

        <!-- Header Controls -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-base font-bold text-slate-800">Danh Sách Trạm Điều Phối</h3>
                <p class="text-xs text-slate-400 font-medium">Nhấp vào một Hub để định vị nhanh trên bản đồ</p>
            </div>
            <button onclick="openCreateModal()" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-xs font-semibold rounded-xl text-white shadow-md shadow-blue-600/10 transition duration-150">
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Khởi tạo Hub mới
            </button>
        </div>

        <!-- Search Form -->
        <form action="{{ route('admin.hubs') }}" method="GET" class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-4 flex items-center gap-4">
            <div class="relative w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên hoặc địa chỉ trạm..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                @if(request('search'))
                    <a href="{{ route('admin.hubs') }}" class="absolute right-3 top-3 text-slate-400 hover:text-slate-650">✕</a>
                @endif
            </div>
            <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                Tìm trạm
            </button>
        </form>

        <!-- Hubs List Card -->
        <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Trạm Trung Chuyển</th>
                            <th class="px-6 py-4">Địa Chỉ Văn Phòng</th>
                            <th class="px-6 py-4 text-right">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse($hubs as $hub)
                        <tr class="hover:bg-slate-50/20 transition cursor-pointer" onclick="focusHub({{ $hub->id }}, {{ $hub->latitude }}, {{ $hub->longitude }})">
                            <td class="px-6 py-4 font-bold text-slate-500">#{{ $hub->id }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                <span>{{ $hub->name }}</span>
                                <span class="block text-[10px] text-slate-400 font-mono mt-0.5">{{ $hub->latitude }}, {{ $hub->longitude }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-xs max-w-[200px] truncate" title="{{ $hub->address }}">{{ $hub->address }}</td>
                            <td class="px-6 py-4 text-right text-xs font-bold space-x-2" onclick="event.stopPropagation()">
                                <button onclick="openEditModal({{ json_encode($hub) }})" class="text-blue-600 hover:text-blue-800 hover:underline">Sửa</button>
                                <form action="{{ route('admin.hubs.destroy', $hub->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa trạm trung chuyển {{ $hub->name }}? Đơn hàng liên kết có thể bị ảnh hưởng!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 hover:underline">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400">Không tìm thấy trạm điều phối (hub) nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($hubs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $hubs->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Map Sidebar (1/3 width on desktop) -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-slate-200/60 p-4 shadow-sm space-y-3 sticky top-6">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bản đồ điểm điều phối</span>
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>
            <!-- Map Container -->
            <div id="hubs_sidebar_map" class="h-[520px] bg-slate-100 rounded-xl border border-slate-200 relative z-10 shadow-inner"></div>
        </div>
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- 1. Create Hub Modal -->
<div id="createModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">Khởi Tạo Trạm Trung Chuyển (Hub)</h3>
            <button onclick="closeModal('createModal')" class="text-slate-400 hover:text-slate-650">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('admin.hubs.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tên trạm trung chuyển *</label>
                <input type="text" name="name" required placeholder="Hub Cầu Giấy, Hub Đống Đa..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Địa chỉ văn phòng *</label>
                <input type="text" name="address" required placeholder="Số 10 Xuân Thủy, Cầu Giấy, Hà Nội" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
            </div>

            <!-- Modal Map integration for selecting location coordinates -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Bản đồ định vị trạm (Nhấp bản đồ để đặt toạ độ)</label>
                <div id="modal_create_map" class="h-44 bg-slate-100 rounded-xl border border-slate-200 relative z-10 mb-2"></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Vĩ độ (Latitude) *</label>
                    <input type="number" step="any" name="latitude" required placeholder="21.036237" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kinh độ (Longitude) *</label>
                    <input type="number" step="any" name="longitude" required placeholder="105.790583" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 text-sm font-semibold rounded-xl text-slate-500 bg-slate-100 hover:bg-slate-200">Hủy</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700">Khởi tạo</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Edit Hub Modal -->
<div id="editModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">Cập Nhật Trạm Trung Chuyển</h3>
            <button onclick="closeModal('editModal')" class="text-slate-400 hover:text-slate-650">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="editHubForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tên trạm trung chuyển *</label>
                <input type="text" id="edit_name" name="name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Địa chỉ văn phòng *</label>
                <input type="text" id="edit_address" name="address" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Bản đồ định vị trạm (Nhấp bản đồ để đặt lại toạ độ)</label>
                <div id="modal_edit_map" class="h-44 bg-slate-100 rounded-xl border border-slate-200 relative z-10 mb-2"></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Vĩ độ (Latitude) *</label>
                    <input type="number" step="any" id="edit_latitude" name="latitude" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kinh độ (Longitude) *</label>
                    <input type="number" step="any" id="edit_longitude" name="longitude" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 text-sm font-semibold rounded-xl text-slate-500 bg-slate-100 hover:bg-slate-200">Hủy</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 font-bold">Cập nhật trạm</button>
            </div>
        </form>
    </div>
</div>

<script>
    let sidebarMap = null;
    let hubMarkers = {};

    let modalCreateMap = null;
    let createMarker = null;

    let modalEditMap = null;
    let editMarker = null;

    document.addEventListener("DOMContentLoaded", function() {
        initSidebarMap();
    });

    function initSidebarMap() {
        sidebarMap = L.map('hubs_sidebar_map').setView([21.028511, 105.804817], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(sidebarMap);

        let hubsData = {!! json_encode($allHubs) !!};
        let markersGroup = [];

        hubsData.forEach(hub => {
            let lat = parseFloat(hub.latitude);
            let lng = parseFloat(hub.longitude);
            if (lat && lng) {
                let marker = L.marker([lat, lng], {
                    icon: L.divIcon({
                        className: '',
                        html: `<div style="background:#ef4444;border:2px solid white;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 6px rgba(0,0,0,0.3);position:relative;">
                                   <svg style="width:16px;height:16px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                   <div style="position:absolute;bottom:-6px;left:50%;transform:translateX(-50%);border-left:5px solid transparent;border-right:5px solid transparent;border-top:7px solid #ef4444;"></div>
                               </div>`,
                        iconSize: [32, 40],
                        iconAnchor: [16, 40]
                    })
                }).addTo(sidebarMap).bindPopup(`<b>${hub.name}</b><br><span class="text-xs text-slate-500">${hub.address}</span>`);
                
                hubMarkers[hub.id] = marker;
                markersGroup.push(marker);
            }
        });

        if (markersGroup.length > 0) {
            let group = new L.featureGroup(markersGroup);
            sidebarMap.fitBounds(group.getBounds().pad(0.15));
        }
    }

    function focusHub(hubId, lat, lng) {
        if (sidebarMap) {
            sidebarMap.setView([lat, lng], 14);
            if (hubMarkers[hubId]) {
                hubMarkers[hubId].openPopup();
            }
        }
    }

    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
        
        // Initialize create modal map
        setTimeout(() => {
            if (!modalCreateMap) {
                modalCreateMap = L.map('modal_create_map').setView([21.028511, 105.804817], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19
                }).addTo(modalCreateMap);

                modalCreateMap.on('click', function(e) {
                    let lat = e.latlng.lat;
                    let lng = e.latlng.lng;
                    document.getElementsByName('latitude')[0].value = lat.toFixed(6);
                    document.getElementsByName('longitude')[0].value = lng.toFixed(6);

                    if (createMarker) modalCreateMap.removeLayer(createMarker);
                    createMarker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: '',
                            html: `<div style="background:#ef4444;border:2px solid white;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 6px rgba(0,0,0,0.3);position:relative;">
                                       <svg style="width:16px;height:16px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                       <div style="position:absolute;bottom:-6px;left:50%;transform:translateX(-50%);border-left:5px solid transparent;border-right:5px solid transparent;border-top:7px solid #ef4444;"></div>
                                   </div>`,
                            iconSize: [32, 40],
                            iconAnchor: [16, 40]
                        })
                    }).addTo(modalCreateMap);
                });
            }
            modalCreateMap.invalidateSize();
        }, 200);
    }

    function openEditModal(hub) {
        document.getElementById('edit_name').value = hub.name;
        document.getElementById('edit_address').value = hub.address;
        document.getElementById('edit_latitude').value = hub.latitude;
        document.getElementById('edit_longitude').value = hub.longitude;

        const form = document.getElementById('editHubForm');
        form.action = `/admin/hubs/${hub.id}`;

        document.getElementById('editModal').classList.remove('hidden');

        // Initialize edit modal map
        setTimeout(() => {
            let lat = parseFloat(hub.latitude) || 21.028511;
            let lng = parseFloat(hub.longitude) || 105.804817;

            if (!modalEditMap) {
                modalEditMap = L.map('modal_edit_map').setView([lat, lng], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19
                }).addTo(modalEditMap);

                modalEditMap.on('click', function(e) {
                    let clickLat = e.latlng.lat;
                    let clickLng = e.latlng.lng;
                    document.getElementById('edit_latitude').value = clickLat.toFixed(6);
                    document.getElementById('edit_longitude').value = clickLng.toFixed(6);

                    if (editMarker) modalEditMap.removeLayer(editMarker);
                    editMarker = L.marker([clickLat, clickLng], {
                        icon: L.divIcon({
                            className: '',
                            html: `<div style="background:#ef4444;border:2px solid white;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 6px rgba(0,0,0,0.3);position:relative;">
                                       <svg style="width:16px;height:16px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                       <div style="position:absolute;bottom:-6px;left:50%;transform:translateX(-50%);border-left:5px solid transparent;border-right:5px solid transparent;border-top:7px solid #ef4444;"></div>
                                   </div>`,
                            iconSize: [32, 40],
                            iconAnchor: [16, 40]
                        })
                    }).addTo(modalEditMap);
                });
            } else {
                modalEditMap.setView([lat, lng], 12);
            }

            if (editMarker) modalEditMap.removeLayer(editMarker);
            editMarker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: '',
                    html: `<div style="background:#ef4444;border:2px solid white;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 6px rgba(0,0,0,0.3);position:relative;">
                               <svg style="width:16px;height:16px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                               <div style="position:absolute;bottom:-6px;left:50%;transform:translateX(-50%);border-left:5px solid transparent;border-right:5px solid transparent;border-top:7px solid #ef4444;"></div>
                           </div>`,
                    iconSize: [32, 40],
                    iconAnchor: [16, 40]
                })
            }).addTo(modalEditMap);

            modalEditMap.invalidateSize();
        }, 200);
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
@endsection
