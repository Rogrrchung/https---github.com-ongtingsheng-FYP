document.addEventListener("DOMContentLoaded", function () {
    // Close error/success messages
    document.querySelectorAll('.error-close, .success-close').forEach(closeButton => {
        closeButton.addEventListener('click', function () {
            const message = this.closest('.error-message, .success-message');
            message.classList.add('fade-out');
            setTimeout(() => {
                message.remove();
            }, 500); // Match the fadeOut animation duration
        });
    });

    // Automatically close messages after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('.error-message, .success-message').forEach(message => {
            message.classList.add('fade-out');
            setTimeout(() => {
                message.remove();
            }, 500);
        });
    }, 5000); // 5 seconds
});