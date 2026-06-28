@extends('layouts.admin')

@section('header_title', 'Quản Lý Người Dùng')

@section('content')

{{-- Stats Bar --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-2xl p-4 text-white shadow-lg">
        <p class="text-[10px] font-bold uppercase opacity-80">Tổng người dùng</p>
        <p class="text-3xl font-extrabold mt-1">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-gradient-to-br from-rose-500 to-rose-600 rounded-2xl p-4 text-white shadow-lg">
        <p class="text-[10px] font-bold uppercase opacity-80">Admin</p>
        <p class="text-3xl font-extrabold mt-1">{{ $stats['admins'] }}</p>
    </div>
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-4 text-white shadow-lg">
        <p class="text-[10px] font-bold uppercase opacity-80">Shipper</p>
        <p class="text-3xl font-extrabold mt-1">{{ $stats['shippers'] }}</p>
    </div>
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-4 text-white shadow-lg">
        <p class="text-[10px] font-bold uppercase opacity-80">Khách hàng</p>
        <p class="text-3xl font-extrabold mt-1">{{ $stats['customers'] }}</p>
    </div>
    <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-4 text-white shadow-lg">
        <p class="text-[10px] font-bold uppercase opacity-80">Đang bị khóa</p>
        <p class="text-3xl font-extrabold mt-1">{{ $stats['inactive'] }}</p>
    </div>
</div>

{{-- Filter Bar --}}
<div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('admin.users') }}" class="flex flex-col md:flex-row gap-3">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên, email, số điện thoại..."
                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <select name="role_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="all">-- Tất cả vai trò --</option>
                @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select name="status" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="all">-- Tất cả trạng thái --</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Bị khóa</option>
            </select>
        </div>
        <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white text-xs font-bold rounded-xl hover:bg-blue-700 transition">Lọc</button>
        <a href="{{ route('admin.users') }}" class="px-5 py-2.5 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-200 transition flex items-center">Reset</a>
    </form>
</div>

{{-- Users Table --}}
<div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h4 class="font-bold text-slate-800 text-sm">Danh Sách Người Dùng</h4>
            <p class="text-[11px] text-slate-400 mt-0.5">Quản lý toàn bộ tài khoản theo phân quyền trong hệ thống</p>
        </div>
        <span class="text-xs text-slate-400 font-medium">Tổng: {{ $users->total() }} người dùng</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <th class="px-6 py-4">ID</th>
                    <th class="px-6 py-4">Tên người dùng</th>
                    <th class="px-6 py-4">Email / SĐT</th>
                    <th class="px-6 py-4 text-center">Vai trò</th>
                    <th class="px-6 py-4 text-center">Trạng thái</th>
                    <th class="px-6 py-4 text-center">Ngày tạo</th>
                    <th class="px-6 py-4 text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50/20 transition">
                    <td class="px-6 py-4 font-mono text-slate-400 text-[10px]">#{{ $user->id }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <span class="h-8 w-8 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-sm
                                {{ $user->role_id == 1 ? 'bg-rose-100 text-rose-700' : ($user->role_id == 2 ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700') }}">
                                {{ strtoupper(substr($user->username, 0, 1)) }}
                            </span>
                            <div>
                                <div class="font-bold text-slate-800">{{ $user->username }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">ID: {{ $user->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-700">{{ $user->email }}</div>
                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $user->phone ?? 'Chưa có SĐT' }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($user->role_id == 1)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                <svg class="h-3 w-3 mr-1 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Admin
                            </span>
                        @elseif($user->role_id == 2)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                <svg class="h-3 w-3 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Shipper
                            </span>
                        @elseif($user->role_id == 3)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <svg class="h-3 w-3 mr-1 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Khách hàng
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">Không xác định</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($user->status)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>Hoạt động
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>Bị khóa
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center text-[10px] text-slate-400 font-medium">
                        {{ $user->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            {{-- Toggle Status --}}
                            <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" title="{{ $user->status ? 'Khóa tài khoản' : 'Kích hoạt' }}"
                                    class="p-1.5 rounded-lg {{ $user->status ? 'bg-amber-50 text-amber-600 hover:bg-amber-100 border border-amber-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border border-emerald-100' }} transition">
                                    @if($user->status)
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    @else
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                    @endif
                                </button>
                            </form>

                            {{-- Edit button --}}
                            <button onclick="openEditUserModal({{ json_encode($user) }})"
                                class="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-100 transition">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>

                            {{-- Delete button (prevent self-delete) --}}
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                onsubmit="return confirm('Xóa người dùng {{ $user->username }}? Hành động này không thể hoàn tác!')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-100 transition">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                        <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                            <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        Không tìm thấy người dùng nào.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $users->links() }}
    </div>
    @endif
</div>

{{-- Edit User Modal --}}
<div id="editUserModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">Chỉnh sửa thông tin người dùng</h3>
            <button onclick="document.getElementById('editUserModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editUserForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Tên đăng nhập *</label>
                    <input type="text" name="username" id="edit_username" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Số điện thoại</label>
                    <input type="text" name="phone" id="edit_phone"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Email *</label>
                <input type="email" name="email" id="edit_email" required
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Vai trò *</label>
                <select name="role_id" id="edit_role_id" required
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Mật khẩu mới (bỏ trống nếu không đổi)</label>
                <input type="password" name="password" placeholder="Tối thiểu 6 ký tự..."
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('editUserModal').classList.add('hidden')"
                    class="px-4 py-2 text-xs font-semibold rounded-xl text-slate-500 bg-slate-100 hover:bg-slate-200">Hủy</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold rounded-xl text-white bg-blue-600 hover:bg-blue-700">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditUserModal(user) {
        document.getElementById('edit_username').value = user.username;
        document.getElementById('edit_phone').value = user.phone || '';
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_role_id').value = user.role_id;
        document.getElementById('editUserForm').action = '/admin/users/' + user.id;
        document.getElementById('editUserModal').classList.remove('hidden');
    }
</script>

@endsection
