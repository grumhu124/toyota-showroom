@extends('layouts.account')
@section('title', 'Quản lý tài khoản')
@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold">Quản lý tài khoản</h1>
            <a href="{{ route('admin.users.create') }}" class="rounded-lg bg-red-700 px-4 py-3 text-sm font-medium text-white">Tạo nhân viên</a>
        </div>
        @if ($errors->any())
            <ul role="alert" class="rounded-lg bg-red-50 p-4 text-sm text-red-700">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        @endif
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-0 flex-1">
                <label for="q" class="mb-2 block text-sm">Tên hoặc email</label>
                <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" class="w-full rounded-lg border border-zinc-300 bg-white p-3">
            </div>
            <div>
                <label for="role" class="mb-2 block text-sm">Vai trò</label>
                <select id="role" name="role" class="rounded-lg border border-zinc-300 bg-white p-3">
                    <option value="">Tất cả</option>
                    @foreach (['customer'=>'Khách hàng', 'sales'=>'Nhân viên', 'admin'=>'Admin'] as $value=>$label)
                        <option value="{{ $value }}" @selected(($filters['role'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button class="rounded-lg bg-zinc-900 px-4 py-3 text-white" type="submit">Tìm</button>
            <a href="{{ route('admin.users.index') }}" class="px-2 py-3 text-sm underline">Xóa lọc</a>
        </form>
        <div class="overflow-x-auto rounded-xl border border-zinc-200 bg-white">
            <table class="w-full text-left text-sm">
                <caption class="sr-only">Danh sách tài khoản trong hệ thống</caption>
                <thead class="bg-zinc-100"><tr><th scope="col" class="p-3">Người dùng</th><th scope="col" class="p-3">Vai trò</th><th scope="col" class="p-3">Trạng thái</th><th scope="col" class="p-3">Thao tác</th></tr></thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="border-t border-zinc-200">
                            <td class="p-3"><p class="font-medium">{{ $user->name }}</p><p>{{ $user->email }}</p><p class="text-zinc-500">{{ $user->phone }}</p></td>
                            <td class="p-3">{{ match($user->role) { \App\Enums\UserRole::Admin => 'Admin', \App\Enums\UserRole::Sales => 'Nhân viên', default => 'Khách hàng' } }}</td>
                            <td class="p-3">{{ $user->is_active ? 'Hoạt động' : 'Đã khóa' }}</td>
                            <td class="p-3">
                                @can('updateStatus', $user)
                                    <form method="POST" action="{{ route('admin.users.status', $user) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="is_active" value="{{ $user->is_active ? '0' : '1' }}">
                                        <button type="submit" class="whitespace-nowrap rounded-lg border border-zinc-300 px-3 py-2">{{ $user->is_active ? 'Khóa tài khoản' : 'Mở tài khoản' }}</button>
                                    </form>
                                @else
                                    <span class="text-zinc-500">Không thay đổi admin</span>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-6 text-center text-zinc-500">Không có tài khoản phù hợp.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
        <p class="text-xs text-zinc-500">Khóa tài khoản giữ lại lịch sử và người phụ trách hồ sơ. Không xóa tài khoản.</p>
    </div>
@endsection
