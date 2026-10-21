@extends('layouts.app')

@section('title', 'Lịch hẹn của tôi — Toyota Showroom')

@section('content')
<section class="page-section">
    <div class="container">
        <p class="eyebrow">TOYOTA SHOWROOM</p>
        <h1>Lịch hẹn của tôi</h1>

        @if (session('status'))
            <div class="notice notice-success" role="status">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="notice notice-error" role="alert">{{ session('error') }}</div>
        @endif

        @forelse ($appointments as $appointment)
            <article class="card">
                <div class="card-body">
                    <span class="eyebrow">{{ $appointment->type === 'viewing' ? 'Xem xe' : 'Bảo dưỡng' }}</span>
                    <h2>{{ $appointment->type->label() }}</h2>
                    <p>Thời gian mong muốn: {{ $appointment->requested_at->format('d/m/Y H:i') }}</p>
                    <p>Trạng thái: <strong>{{ $appointment->status->label() }}</strong></p>
                    @if ($appointment->assignedStaff)
                        <p>Nhân viên phụ trách: {{ $appointment->assignedStaff->name }}</p>
                    @endif
                    @if ($appointment->car)
                        <p>Xe: {{ $appointment->car->name }}</p>
                    @endif
                    @if ($appointment->service)
                        <p>Dịch vụ: {{ $appointment->service->name }}</p>
                    @endif
                    @if ($appointment->status === \App\Enums\AppointmentStatus::Pending)
                        <form method="POST" action="{{ route('customer.appointments.reschedule', $appointment) }}" class="stack-form">
                            @csrf
                            <label for="reschedule_at-{{ $appointment->id }}">Đổi thời gian mong muốn</label>
                            <input type="datetime-local" name="requested_at" id="reschedule_at-{{ $appointment->id }}" required>
                            <button type="submit" class="button button-dark">Đổi lịch</button>
                        </form>
                    @endif
                    @if ($appointment->status === \App\Enums\AppointmentStatus::Pending || $appointment->status === \App\Enums\AppointmentStatus::Confirmed)
                        <form method="POST" action="{{ route('customer.appointments.cancel', $appointment) }}" onsubmit="return confirm('Xác nhận hủy lịch hẹn này?');">
                            @csrf
                            <label for="cancel_reason-{{ $appointment->id }}">Lý do hủy</label>
                            <input type="text" name="cancel_reason" id="cancel_reason-{{ $appointment->id }}" maxlength="500" required>
                            <button type="submit" class="button button-dark">Hủy lịch</button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <p>Bạn chưa có lịch hẹn nào. <a href="{{ route('appointments.create') }}">Đặt lịch ngay</a>.</p>
        @endforelse

        {{ $appointments->links() }}
    </div>
</section>
@endsection
