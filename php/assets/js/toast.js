(function () {
    const container = document.getElementById('toast-container');
    window.showToast = function (message, type = 'success') {
        if (!container || typeof message !== 'string' || message.trim() === '') {
            return;
        }

        const toastType = type === 'error' ? 'error' : 'success';
        const toast = document.createElement('div');
        toast.className = `alert alert-${toastType} pointer-events-auto shadow-lg`;
        toast.setAttribute('role', toastType === 'error' ? 'alert' : 'status');

        const text = document.createElement('span');
        text.textContent = message;
        text.style.whiteSpace = 'pre-line';

        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'btn btn-sm btn-ghost btn-square';
        closeButton.setAttribute('aria-label', 'Close notification');
        closeButton.textContent = '×';

        let timeoutId;
        const dismiss = function () {
            window.clearTimeout(timeoutId);
            toast.remove();
        };

        closeButton.addEventListener('click', dismiss);
        toast.append(text, closeButton);
        container.append(toast);

        timeoutId = window.setTimeout(dismiss, 4000);
    };
})();
