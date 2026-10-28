const chat = document.querySelector('[data-customer-chat]');
if (chat) {
    const conversations = [...chat.querySelectorAll('[data-conversation]')];
    const threads = [...chat.querySelectorAll('[data-chat-thread]')];
    conversations.forEach((button) => {
        button.addEventListener('click', () => {
            conversations.forEach((item) => {
                const selected = item === button;
                item.classList.toggle('is-selected', selected);
                item.setAttribute('aria-current', String(selected));
            });
            threads.forEach((thread) => { thread.hidden = thread.dataset.chatThread !== button.dataset.conversation; });
            chat.classList.add('is-thread-open');
            // Each thread keeps its own unsent draft in the DOM; nothing persists across reload.
            chat.querySelector('[data-chat-thread]:not([hidden]) h2')?.focus();
        });
    });
    threads.forEach((thread) => {
        thread.querySelector('h2').tabIndex = -1;
        thread.querySelector('[data-chat-back]').addEventListener('click', () => {
            chat.classList.remove('is-thread-open');
            chat.querySelector('[data-conversation][aria-current="true"]')?.focus();
        });
        const form = thread.querySelector('[data-chat-composer]');
        const draft = form.querySelector('[data-chat-draft]');
        const feedback = form.querySelector('[data-chat-feedback]');
        draft.addEventListener('input', () => {
            form.querySelector('[data-chat-count]').textContent = `${draft.value.length} / 1000`;
            feedback.hidden = true;
        });
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            if (draft.disabled) return;
            feedback.textContent = draft.value.trim()
                ? 'Chưa gửi: đây là bản xem trước, chưa kết nối với showroom. Nội dung vẫn được giữ trong ô soạn tin.'
                : 'Bạn hãy nhập nội dung tin nhắn trước.';
            feedback.hidden = false;
            draft.focus();
        });
    });
    const search = chat.querySelector('[data-chat-search]');
    const normalize = (value) => value.toLocaleLowerCase('vi').normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd');
    search.addEventListener('input', () => {
        const query = normalize(search.value.trim());
        conversations.forEach((button) => { button.hidden = !normalize(button.textContent).includes(query); });
        chat.querySelector('[data-chat-search-empty]').hidden = conversations.some((button) => !button.hidden);
    });
}
