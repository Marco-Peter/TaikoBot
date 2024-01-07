function receivePushNotification(event) {
    console.log("[Service Worker] Push Received.");

    const data = event.data?.json() ?? { str: "Nix Drinn!"};
    console.log(data);

    //call the method showNotification to show the notification
    event.waitUntil(self.registration.showNotification(data.title, data));
}

function clickPushNotification(event) {
    console.log("[Service Worker] Notification click Received.", event.notification.data);

    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data["url"]));
}

self.addEventListener("notificationclick", clickPushNotification);
self.addEventListener("push", receivePushNotification);
