if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => {
                if (reg.installing) {
                    console.log("Service worker installing");
                } else if (reg.waiting) {
                    console.log("Service worker installed");
                } else if (reg.active) {
                    console.log("Service worker active");
                }
            })
            .catch(error => {
                console.error(`Service Worker registration error (${error})`);
            });
    });
} else {
    console.warn('Service Worker not available');
}
