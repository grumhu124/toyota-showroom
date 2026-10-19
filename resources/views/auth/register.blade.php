@extends('layouts.account')
@section('title', 'Đăng ký')
@section('content')
    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-2xl font-semibold">Tạo tài khoản khách hàng</h1>
        <p class="mt-2 text-sm text-zinc-600">Bạn vẫn có thể xem xe và gửi yêu cầu mà không cần tài khoản.</p>
        <form method="POST" action="{{ route('register.store') }}" class="mt-7 space-y-5">
            @csrf
            @foreach (['name' => ['Họ và tên', 'text', 'name', 100], 'email' => ['Email', 'email', 'username', 255], 'phone' => ['Số điện thoại', 'tel', 'tel-national', 10]] as $field => [$label, $type, $autocomplete, $max])
                <div>
                    <label for="{{ $field }}" class="mb-2 block text-sm font-medium">{{ $label }}</label>
                    <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" value="{{ old($field) }}" required maxlength="{{ $max }}" autocomplete="{{ $autocomplete }}"
                        @if ($field === 'phone') inputmode="tel" pattern="0[3-9][0-9]{8}" @endif
                        @error($field) aria-invalid="true" aria-describedby="{{ $field }}-error" @enderror
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2.5 focus:outline-red-700">
                    @error($field) <p id="{{ $field }}-error" role="alert" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
            @endforeach
            @foreach (['password' => 'Mật khẩu', 'password_confirmation' => 'Xác nhận mật khẩu'] as $field => $label)
                <div>
                    <label for="{{ $field }}" class="mb-2 block text-sm font-medium">{{ $label }}</label>
                    <input id="{{ $field }}" name="{{ $field }}" type="password" required minlength="8" autocomplete="new-password"
                        @error($field) aria-invalid="true" aria-describedby="{{ $field }}-error" @enderror
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2.5 focus:outline-red-700">
                    @error($field) <p id="{{ $field }}-error" role="alert" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
            @endforeach
            <p class="text-xs text-zinc-500">Mật khẩu có ít nhất 8 ký tự.</p>
            <button type="submit" class="w-full rounded-lg bg-red-700 px-4 py-3 font-medium text-white hover:bg-red-800">Tạo tài khoản</button>
        </form>
        <p class="mt-6 text-sm text-zinc-600">Đã có tài khoản? <a href="{{ route('login') }}" class="font-medium text-red-700 underline">Đăng nhập</a></p>
    </div>
@endsection
