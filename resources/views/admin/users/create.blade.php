@extends('layouts.account')
@section('title', 'Tạo nhân viên')
@section('content')
    <div class="rounded-xl border border-zinc-200 bg-white p-6">
        <h1 class="text-2xl font-semibold">Tạo tài khoản nhân viên</h1>
        <p class="mt-2 text-sm text-zinc-600">Vai trò được cố định là nhân viên. Mật khẩu ban đầu được bàn giao riêng.</p>
        <form method="POST" action="{{ route('admin.users.store') }}" class="mt-6 space-y-5">
            @csrf
            @foreach (['name' => ['Họ tên', 'text', 100], 'email' => ['Email', 'email', 255], 'phone' => ['Điện thoại', 'tel', 10], 'password' => ['Mật khẩu', 'password', 72], 'password_confirmation' => ['Xác nhận mật khẩu', 'password', 72]] as $field => [$label, $type, $max])
                <div>
                    <label for="{{ $field }}" class="mb-2 block text-sm font-medium">{{ $label }}</label>
                    <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" required maxlength="{{ $max }}"
                        @if ($type !== 'password') value="{{ old($field) }}" @else autocomplete="new-password" @endif
                        @error($field) aria-invalid="true" aria-describedby="{{ $field }}-error" @enderror
                        class="w-full rounded-lg border border-zinc-300 p-3">
                    @error($field)<p id="{{ $field }}-error" role="alert" class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
            @endforeach
            <p class="text-xs text-zinc-500">Mật khẩu ít nhất 8 ký tự, tối đa 72 byte.</p>
            <button type="submit" class="rounded-lg bg-red-700 px-4 py-3 font-medium text-white">Tạo nhân viên</button>
            <a href="{{ route('admin.users.index') }}" class="ml-3 text-sm underline">Về danh sách</a>
        </form>
    </div>
@endsection
