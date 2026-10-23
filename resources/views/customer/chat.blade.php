@extends('layouts.customer')
@section('title', 'Tin nhắn tư vấn')
@section('content')
<div class="customer-page-heading"><div><p class="customer-eyebrow">MY TOYOTA / KẾT NỐI</p><h1>Tin nhắn tư vấn</h1></div><span class="customer-pill customer-pill-preview">Bản xem trước giao diện</span></div>
<p class="customer-chat-disclaimer">Các cuộc tư vấn bên dưới là dữ liệu minh họa. Chưa gửi hoặc nhận tin nhắn thật với showroom.</p>
<section class="customer-chat" data-customer-chat aria-label="Không gian tin nhắn minh họa">
    <aside class="chat-conversations" aria-label="Danh sách cuộc tư vấn">
        <div class="chat-list-heading"><h2>Cuộc tư vấn</h2><span>{{ count($conversations) }} mẫu</span></div>
        <label class="chat-search"><x-customer-icon name="search"/><input type="search" placeholder="Tìm cuộc tư vấn…" aria-label="Tìm cuộc tư vấn" data-chat-search></label>
        <div class="chat-conversation-list">
            @foreach($conversations as $conversation)
                <button class="chat-conversation {{ $loop->first ? 'is-selected' : '' }}" type="button" data-conversation="{{ $conversation['key'] }}" aria-current="{{ $loop->first ? 'true' : 'false' }}" aria-controls="thread-{{ $conversation['key'] }}">
                    <span class="chat-conversation-avatar">{{ $conversation['initial'] }}</span><span class="chat-conversation-copy"><strong>{{ $conversation['name'] }}</strong><span>{{ $conversation['summary'] }}</span><small class="chat-status chat-status-{{ $conversation['status'] }}">{{ $conversation['status_label'] }}</small></span>
                </button>
            @endforeach
        </div>
        <p class="chat-search-empty" data-chat-search-empty hidden role="status">Không có cuộc tư vấn phù hợp.</p>
        <div class="chat-list-note"><x-customer-icon name="shield"/><p>Mỗi cuộc tư vấn là một<br>không gian trao đổi riêng.</p></div>
    </aside>
    <div class="chat-thread-area">
        @foreach($conversations as $conversation)
        <section class="chat-thread" id="thread-{{ $conversation['key'] }}" data-chat-thread="{{ $conversation['key'] }}" @if(!$loop->first) hidden @endif aria-labelledby="title-{{ $conversation['key'] }}">
            <header class="chat-thread-header">
                <button class="chat-mobile-back" type="button" data-chat-back aria-label="Quay lại danh sách cuộc tư vấn"><x-customer-icon name="back"/></button>
                <span class="chat-advisor-avatar">T</span><div><h2 id="title-{{ $conversation['key'] }}">{{ $conversation['advisor'] }}</h2><p>{{ $conversation['name'] }} <span>· {{ $conversation['reference'] }}</span></p></div><span class="chat-connection"><i></i> Chưa kết nối</span>
            </header>
            <div class="chat-vehicle"><img src="{{ asset($conversation['image']) }}" alt="{{ $conversation['name'] }} minh họa" width="120" height="60"><div><span>XE BẠN QUAN TÂM</span><strong>{{ $conversation['name'] }}</strong></div><span class="chat-status chat-status-{{ $conversation['status'] }}">{{ $conversation['status_label'] }}</span></div>
            <div class="chat-messages" role="log" aria-label="Tin nhắn minh họa của {{ $conversation['name'] }}">
                @if(count($conversation['messages']))
                    <p class="chat-date">Hội thoại minh họa</p>
                    @foreach($conversation['messages'] as $message)
                        <div class="chat-message chat-message-{{ $message['sender'] }}"><div><p>{{ $message['content'] }}</p></div><span>{{ $message['sender'] === 'customer' ? 'Bạn (minh họa)' : 'Tư vấn viên mẫu' }} · {{ $message['time'] }}</span></div>
                    @endforeach
                @else
                    <div class="chat-waiting"><x-customer-icon name="chat"/><h3>Chờ một lời chào mới</h3><p>Cuộc tư vấn sẽ bắt đầu khi showroom phân công nhân viên phụ trách.</p><small>Đây là trạng thái minh họa.</small></div>
                @endif
            </div>
            <form class="chat-composer" data-chat-composer>
                <p class="chat-send-feedback" data-chat-feedback role="alert" hidden></p>
                @if($conversation['status'] === 'new')
                    <p class="chat-readonly-note">Cuộc tư vấn đang chờ phân công. Bạn chưa thể gửi tin nhắn.</p>
                @elseif($conversation['status'] === 'closed')
                    <p class="chat-readonly-note">Cuộc tư vấn đã kết thúc. Bạn có thể xem lại nội dung trao đổi.</p>
                @endif
                <div class="chat-compose-row"><label class="sr-only" for="draft-{{ $conversation['key'] }}">Nội dung tin nhắn</label><textarea id="draft-{{ $conversation['key'] }}" rows="2" maxlength="1000" placeholder="{{ $conversation['status'] === 'in_progress' ? 'Viết lời nhắn của bạn…' : 'Chưa thể gửi tin nhắn' }}" data-chat-draft @disabled($conversation['status'] !== 'in_progress') aria-describedby="hint-{{ $conversation['key'] }}"></textarea><button type="submit" class="chat-send-button" @disabled($conversation['status'] !== 'in_progress')><x-customer-icon name="send"/><span>Gửi</span></button></div>
                <div class="chat-compose-hint" id="hint-{{ $conversation['key'] }}"><span>Bản mẫu · Nội dung chưa được gửi đến showroom.</span><span data-chat-count>0 / 1000</span></div>
            </form>
        </section>
        @endforeach
    </div>
</section>
<noscript><p class="customer-chat-disclaimer">Bật JavaScript để chuyển giữa các cuộc tư vấn. Bản mẫu chưa hỗ trợ gửi tin thật.</p></noscript>
@endsection
