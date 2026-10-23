@extends('layouts.customer')
@section('title', 'Tài khoản của tôi')
@section('content')
    <div class="customer-page-heading"><div><p class="customer-eyebrow">MY TOYOTA / TỔNG QUAN</p><h1>Tài khoản của tôi</h1></div><span class="customer-pill"><i></i> Tài khoản đang hoạt động</span></div>
    <section class="customer-welcome" aria-labelledby="welcome-title">
        <img src="{{ asset('assets/home/toyota-driving.jpg') }}" width="6000" height="3487" alt="Xe Toyota đang chuyển động trên đường" fetchpriority="high">
        <div><p class="customer-eyebrow">MỖI HÀNH TRÌNH, MỘT DẤU ẤN</p><h2 id="welcome-title">Xin chào,<br><strong>{{ auth()->user()->name }}.</strong></h2><p>Chiếc xe phù hợp. Những trải nghiệm mới.<br>Hành trình tiếp theo bắt đầu từ bạn.</p><a class="customer-button customer-button-red" href="{{ url('/xe') }}">Khám phá các dòng xe <x-customer-icon name="arrow"/></a></div>
    </section>
    <div class="customer-account-grid">
        <section class="customer-panel customer-profile" aria-labelledby="profile-title">
            <div class="customer-section-heading"><div><p class="customer-eyebrow">01 / HỒ SƠ</p><h2 id="profile-title">Thông tin cá nhân</h2></div><span class="customer-profile-mark"><x-customer-icon name="shield"/></span></div>
            <dl><div><dt>Họ và tên</dt><dd>{{ auth()->user()->name }}</dd></div><div><dt>Địa chỉ email</dt><dd>{{ auth()->user()->email }}</dd></div><div><dt>Số điện thoại</dt><dd>{{ auth()->user()->phone ?: 'Chưa cập nhật' }}</dd></div><div><dt>Loại tài khoản</dt><dd>Khách hàng</dd></div></dl>
            <p class="customer-muted customer-profile-note">Thông tin được sử dụng cho các trao đổi và lịch hẹn với showroom.</p>
        </section>
        <section class="customer-panel customer-next" aria-labelledby="next-title">
            <p class="customer-eyebrow">02 / ĐỒNG HÀNH</p><h2 id="next-title">Kết nối với showroom</h2>
            @if(Route::has('customer.chat.preview'))
                <a class="customer-service-link" href="{{ route('customer.chat.preview') }}"><span class="customer-service-icon"><x-customer-icon name="chat"/></span><span><strong>Tin nhắn tư vấn <small>Bản mẫu</small></strong><span>Xem trước không gian trao đổi cùng tư vấn viên.</span></span><x-customer-icon name="arrow"/></a>
            @else
                <p class="customer-muted">Chức năng tin nhắn đang được hoàn thiện.</p>
            @endif
            <div class="customer-service-link customer-service-pending"><span class="customer-service-icon"><x-customer-icon name="calendar"/></span><span><strong>Lịch hẹn của bạn <small>Sắp có</small></strong><span>Lịch xem xe và bảo dưỡng sẽ xuất hiện tại đây khi chức năng sẵn sàng.</span></span></div>
            <p class="customer-muted customer-next-note">Tư vấn và lịch hẹn đang được hoàn thiện. Chưa có lịch sử hoặc số liệu để hiển thị.</p>
        </section>
    </div>
@endsection
