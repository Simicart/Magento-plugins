<?php
/**
 * Created by PhpStorm.
 * User: scott
 * Date: 1/29/18
 * Time: 9:23 PM
 */

namespace Simi\Simipwa\Observer;

use Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\ObjectManagerInterface as ObjectManager;
use Magento\Framework\App\Filesystem\DirectoryList;

class Createswfile implements ObserverInterface
{
    /**
     * change api
     * @param Observer $observer
     */

    public function __construct(ObjectManager $simiObjectManager)
    {
        $this->simiObjectManager = $simiObjectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $swcontent = "
        self.addEventListener('push', function(event) {
        var apiPath = './simipwa/index/message?endpoint=';
        event.waitUntil(
            registration.pushManager.getSubscription()
                .then(function(subscription) {
                    if (!subscription || !subscription.endpoint) {
                        throw new Error();
                    }

                    apiPath = apiPath + encodeURI(subscription.endpoint);
                    return fetch(apiPath)
                        .then(function(response) {
                            if (response.status !== 200){
                                console.log('Problem Occurred:'+response.status);
                                throw new Error();
                            }
                            return response.json();
                        })
                        .then(function(data) {
                            if (data.status == 0) {
                                console.error('The API returned an error.', data.error.message);
                                throw new Error();
                            }
                            //console.log(data);
                            var options = {};
                            var title = '';
                            if (data.notification.notice_title){

                                title = data.notification.notice_title;
                                var message = data.notification.notice_content;
                                var icon = 'https://www.simicart.com/skin/frontend/default/simicart2.0/images/simicart/logo2.png';
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
                                    icon: './skin/frontend/default/pwa/icon.png',
                                    badge: './skin/frontend/default/pwa/badge.png',
                                    data: {
                                        url: '/'
                                    }
                                };
                            }

                            return self.registration.showNotification(title, options);
                        })
                        .catch(function(err) {
                            console.log(err);
                            return self.registration.showNotification('New Notification', {
                                icon: './skin/frontend/default/pwa/icon.png',
                                badge: './skin/frontend/default/pwa/badge.png',
                                data: {
                                    url: '/'
                                }
                            });
                        });
                })
        );
    });
    self.addEventListener('notificationclick', function(event) {
        event.notification.close();
        var url = event.notification.data.url;
        event.waitUntil(
            clients.matchAll({
                type: 'window'
            })
                .then(function(windowClients) {
                    for (var i = 0; i < windowClients.length; i++) {
                        var client = windowClients[i];
                        if (client.url === url && 'focus' in client) {
                            return client.focus();
                        }
                    }
                    if (clients.openWindow) {
                        return clients.openWindow(url);
                    }
                })
        );
    });";
        $rootPath = $this->simiObjectManager->get('\Magento\Framework\App\Filesystem\DirectoryList')
        ->getPath(DirectoryList::ROOT) . \DIRECTORY_SEPARATOR;
        try {
            if (!file_exists($rootPath . 'sw.js')) {
                file_put_contents($rootPath . 'sw.js', $swcontent);
                chmod($rootPath . 'sw.js', 0777);
            }
        } catch (\Exception $exception) {

        }
    }
}
