/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 11/16/18
 * Time: 9:08 AM
 */
self.addEventListener(
    'push', function (event) {
        var apiPath = './simipush/index/message?endpoint=';
        event.waitUntil(
            registration.pushManager.getSubscription()
                .then(
                    function (subscription) {
                        if (!subscription || !subscription.endpoint) {
                            throw new Error();
                        }

                        apiPath = apiPath + encodeURI(subscription.endpoint);
                        return fetch(apiPath)
                            .then(
                                function (response) {
                                    if (response.status !== 200){
                                        console.log("Problem Occurred:"+response.status);
                                        throw new Error();
                                    }

                                    return response.json();
                                }
                            )
                            .then(
                                function (data) {
                                    if (data.status == 0) {
                                        console.error('The API returned an error.', data.error.message);
                                        throw new Error();
                                    }

                                    //console.log(data);
                                    var options = {};
                                    var title = '';
                                    var icon = data.notification.logo_icon;
                                    if (data.notification.notice_title){
                                        title = data.notification.notice_title;
                                        var message = data.notification.notice_content;
                                        var url = '/';
                                        if (data.notification.notice_url) {
                                            url = data.notification.notice_url;
                                        }

                                        if (data.notification.image_url){
                                            options['image'] = data.notification.image_url;
                                        }

                                        var data = {
                                            url: url
                                        };
                                        options = {
                                            body : message,
                                            icon: icon,
                                            data: data
                                        };
                                    } else {
                                        title = 'New Notification';
                                        options = {
                                            icon : icon,
                                            data: {
                                                url: "/"
                                            }
                                        };
                                    }

                                    return self.registration.showNotification(title, options);
                                }
                            )
                            .catch(
                                function (err) {
                                    console.log(err);
                                    return self.registration.showNotification(
                                        'New Notification', {
                                            icon: icon,
                                            data: {
                                                url: "/"
                                            }
                                        }
                                    );
                                }
                            );
                    }
                )
        );
    }
);
self.addEventListener(
    'notificationclick', function (event) {
        event.notification.close();
        var url = event.notification.data.url;
        event.waitUntil(
            clients.matchAll(
                {
                    type: 'window'
                }
            )
                .then(
                    function (windowClients) {
                        for (var i = 0; i < windowClients.length; i++) {
                            var client = windowClients[i];
                            if (client.url === url && 'focus' in client) {
                                return client.focus();
                            }
                        }

                        if (clients.openWindow) {
                            return clients.openWindow(url);
                        }
                    }
                )
        );
    }
);