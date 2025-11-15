// assets/js/utils.js
function showNotification(message, isError = false, containerId = 'notification') {
    const notificationContainer = document.getElementById(containerId);
    if (!notificationContainer) return;
    const toastId = 'toast-' + Date.now();
    const toastElement = document.createElement('div');
    toastElement.id = toastId;
    toastElement.className = 'toast hide';
    toastElement.setAttribute('role', 'alert');
    toastElement.setAttribute('aria-live', 'assertive');
    toastElement.setAttribute('aria-atomic', 'true');
    toastElement.setAttribute('data-bs-delay', '4000');
    toastElement.innerHTML = `
        <div class="toast-header">
            <i class="material-symbols-rounded me-2 ${isError ? 'text-danger' : 'text-success'}">${isError ? 'error' : 'check_circle'}</i>
            <strong class="me-auto">${isError ? 'Gagal' : 'Sukses'}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">${message}</div>`;
    notificationContainer.appendChild(toastElement);
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    toastElement.addEventListener('hidden.bs.toast', () => toastElement.remove());
}