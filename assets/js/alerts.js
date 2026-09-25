function alerta(mensaje, tipo = 'info', duracion = 5000) {
    // Create alert container if it doesn't exist
    let alertContainer = document.getElementById('alert-container');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'alert-container';
        alertContainer.style.position = 'fixed';
        alertContainer.style.top = '20px';
        alertContainer.style.right = '20px';
        alertContainer.style.zIndex = '9999';
        document.body.appendChild(alertContainer);
    }

    // Map alert types to Bootstrap icons
    const iconMap = {
        'success': 'bi-check-circle-fill',
        'danger': 'bi-exclamation-triangle-fill',
        'warning': 'bi-exclamation-circle-fill',
        'info': 'bi-info-circle-fill',
        'primary': 'bi-info-circle-fill',
        'secondary': 'bi-info-circle-fill'
    };

    const icon = iconMap[tipo] || 'bi-info-circle-fill';

    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${tipo} alert-dismissible fade show`;
    alertDiv.setAttribute('role', 'alert');
    alertDiv.style.minWidth = '300px';
    alertDiv.style.marginBottom = '10px';
    alertDiv.style.position = 'relative';
    alertDiv.style.overflow = 'hidden';

    alertDiv.innerHTML = `
        <i class="bi ${icon} me-2"></i>${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <div class="progress" style="height: 3px; position: absolute; bottom: 0; left: 0; right: 0; background: transparent;">
            <div class="progress-bar bg-${tipo}" role="progressbar" style="width: 100%; transition: width ${duracion}ms linear;"></div>
        </div>
    `;

    // Add to container
    alertContainer.appendChild(alertDiv);

    // Start progress bar animation
    const progressBar = alertDiv.querySelector('.progress-bar');
    if (duracion > 0) {
        setTimeout(() => {
            progressBar.style.width = '0%';
        }, 10);

        // Auto dismiss
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => {
                alertDiv.remove();
            }, 150);
        }, duracion);
    }

    // Manual dismiss handler
    const closeBtn = alertDiv.querySelector('.btn-close');
    closeBtn.addEventListener('click', () => {
        alertDiv.classList.remove('show');
        setTimeout(() => {
            alertDiv.remove();
        }, 150);
    });
}
