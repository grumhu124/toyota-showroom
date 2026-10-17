const imageDialog = document.querySelector('[data-image-dialog]');
if (imageDialog) {
    document.querySelectorAll('[data-gallery-open]').forEach((button) => {
        button.addEventListener('click', () => imageDialog.showModal());
    });
    imageDialog.addEventListener('click', (event) => {
        if (event.target === imageDialog) imageDialog.close();
    });
}
