@extends('layouts.app')

@section('title', 'Dịch vụ — Toyota Showroom')

@section('content')
<section class="page-section">
    <div class="container">
        <p class="eyebrow">TOYOTA SHOWROOM</p>
        <h1>Dịch vụ &amp; bảo dưỡng</h1>
        <p>Tổng quan các dịch vụ bảo hành và bảo dưỡng chính hãng Toyota của showroom.</p>

        <div class="card-grid">
            @forelse($services as $service)
                <article class="card">
                    <div class="card-body">
                        <span class="eyebrow">{{ $service->type === 'warranty' ? 'Bảo hành' : 'Bảo dưỡng' }}</span>
                        <h2>{{ $service->name }}</h2>
                        <p>{{ $service->description }}</p>
                        @if ($service->type === 'maintenance' && $service->allow_booking)
                            <a class="button button-red" href="{{ route('appointments.create', ['type' => 'maintenance']) }}">Đặt lịch bảo dưỡng</a>
                        @else
                            <a class="button button-dark" href="{{ url('/').'#lien-he' }}">Liên hệ showroom</a>
                        @endif
                    </div>
                </article>
            @empty
                <p>Chưa có dịch vụ nào được công bố. Vui lòng liên hệ showroom.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
