@extends('layouts.account')
@section('title', 'Tài khoản của tôi')
@section('content')
    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8">
        <p class="text-sm font-medium text-red-700">TÀI KHOẢN</p>
        <h1 class="mt-2 break-words text-2xl font-semibold">Xin chào, {{ auth()->user()->name }}</h1>
        <dl class="mt-6 space-y-4 text-sm">
            <div><dt class="text-zinc-500">Email</dt><dd class="mt-1 break-all">{{ auth()->user()->email }}</dd></div>
            <div><dt class="text-zinc-500">Số điện thoại</dt><dd class="mt-1">{{ auth()->user()->phone ?: 'Chưa cập nhật' }}</dd></div>
            <div><dt class="text-zinc-500">Vai trò</dt><dd class="mt-1">{{ match(auth()->user()->role) {
                \App\Enums\UserRole::Admin => 'Quản trị viên',
                \App\Enums\UserRole::Sales => 'Nhân viên tư vấn',
                default => 'Khách hàng',
            } }}</dd></div>
        </dl>
        <p class="mt-6 rounded-lg bg-zinc-50 p-4 text-sm text-zinc-600">Tài khoản đã sẵn sàng. Các mục tư vấn và lịch hẹn đang được hoàn thiện.</p>
        <a href="{{ url('/') }}" class="mt-6 inline-block text-sm font-medium text-red-700 underline">Về trang chủ</a>
    </div>
@endsection
