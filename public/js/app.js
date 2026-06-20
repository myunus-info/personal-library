//  Library Interactions

document.addEventListener('DOMContentLoaded', () => {
    // Alert dismissal
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        const closeBtn = alert.querySelector('.close-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                alert.classList.add('opacity-0', 'transition-all', 'duration-300', 'scale-95');
                setTimeout(() => {
                    alert.remove();
                }, 300);
            });
        }
    });

    // Fade out notifications automatically after 5 seconds
    setTimeout(() => {
        alerts.forEach(alert => {
            alert.classList.add('opacity-0', 'transition-all', 'duration-500', 'scale-95');
            setTimeout(() => {
                alert.remove();
            }, 500);
        });
    }, 5000);
});
