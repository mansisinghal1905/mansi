
importScripts('https://www.gstatic.com/firebasejs/8.3.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.0/firebase-messaging.js');

    firebase.initializeApp({
        apiKey: "AIzaSyDoKVbTIEkd0B8fG3xvIFLW7BkPmXiWrYM",
        authDomain: "office-84692.firebaseapp.com",
        projectId: "office-84692",
        storageBucket: "office-84692.appspot.com",
        messagingSenderId: "38369792112",
        appId: "1:38369792112:web:fe09efb05c9121df97a4b5",
        measurementId: "G-VM83S4MPDV"
    });
    const messaging = firebase.messaging();
    messaging.setBackgroundMessageHandler(function(payload) {
    // console.log(
    //     "[firebase-messaging-sw.js] Received background message ",
    //     payload,
    // );

    const notificationTitle = "Background Message Title";
    const notificationOptions = {
        body: "Background Message body.",
      //  icon: "/itwonders-web-logo.png",
    };

    return self.registration.showNotification(
        notificationTitle,
        notificationOptions,
    );
});