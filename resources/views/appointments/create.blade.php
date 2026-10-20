@extends('layouts.app')

@section('title', 'Đặt lịch hẹn — Toyota Showroom')

@section('content')
<section class="page-section">
    <div class="container container-narrow">
        <p class="eyebrow">TOYOTA SHOWROOM</p>
        <h1>Đặt lịch hẹn</h1>
        <p>Chọn lịch xem xe hoặc bảo dưỡng. Showroom sẽ gọi điện xác nhận thời gian cụ thể.</p>

        @if (session('status'))
            <div class="notice notice-success" role="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('appointments.store') }}" class="stack-form">
            @csrf

            <fieldset>
                <legend>Loại lịch hẹn</legend>
                <label class="radio-row">
                    <input type="radio" name="type" value="viewing" @checked(old('type', $preselectType) === 'viewing') required>
                    <span>Lịch xem xe tại showroom</span>
                </label>
                <label class="radio-row">
                    <input type="radio" name="type" value="maintenance" @checked(old('type', $preselectType) === 'maintenance') required>
                    <span>Lịch bảo dưỡng / bảo hành</span>
                </label>
                @error('type') <p class="field-error">{{ $message }}</p> @enderror
            </fieldset>

            <div class="field-group" data-appointment-target="viewing">
                <label for="car_id">Xe muốn xem</label>
                <select name="car_id" id="car_id">
                    <option value="">— Chọn xe —</option>
                    @foreach ($cars as $car)
                        <option value="{{ $car->id }}" @selected(old('car_id') == $car->id)>{{ $car->name }}</option>
                    @endforeach
                </select>
                @error('car_id') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field-group" data-appointment-target="maintenance">
                <label for="service_id">Dịch vụ</label>
                <select name="service_id" id="service_id">
                    <option value="">— Chọn dịch vụ —</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>{{ $service->name }}</option>
                    @endforeach
                </select>
                @error('service_id') <p class="field-error">{{ $message }}</p> @enderror

                <label for="customer_car_info">Thông tin xe của bạn (mẫu xe, năm, biển số)</label>
                <input type="text" name="customer_car_info" id="customer_car_info" maxlength="200" value="{{ old('customer_car_info') }}">
                @error('customer_car_info') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field-group">
                <label for="contact_name">Họ tên liên hệ</label>
                <input type="text" name="contact_name" id="contact_name" maxlength="100" required value="{{ old('contact_name', auth()->user()?->name) }}">
                @error('contact_name') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field-group">
                <label for="contact_phone">Số điện thoại</label>
                <input type="tel" name="contact_phone" id="contact_phone" maxlength="10" required value="{{ old('contact_phone', auth()->user()?->phone) }}">
                @error('contact_phone') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field-group">
                <label for="requested_at">Thời gian mong muốn</label>
                <input type="datetime-local" name="requested_at" id="requested_at" required value="{{ old('requested_at') }}">
                @error('requested_at') <p class="field-error">{{ $message }}</p> @enderror
                <p class="field-hint">Giờ tiếp nhận demo của showroom, xác nhận qua điện thoại.</p>
            </div>

            <div class="field-group">
                <label for="note">Ghi chú thêm (tùy chọn)</label>
                <textarea name="note" id="note" rows="3" maxlength="1000">{{ old('note') }}</textarea>
                @error('note') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="button button-red">Gửi yêu cầu đặt lịch</button>
        </form>
    </div>
</section>
@endsection
