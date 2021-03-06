<?php
/**
 * Created by PhpStorm.
 * User: scott
 * Date: 1/30/18
 * Time: 3:47 PM
 */

namespace Simi\Simipushnotif\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;

class Register extends \Magento\Framework\App\Action\Action
{

    public $storeManager;

    public function __construct(Context $context)
    {
        parent::__construct($context);
        $this->storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Exception
     */
    public function execute()
    {
        $this->zendRequest = $this->_objectManager->get('Simi\Simipushnotif\Helper\RequestHttp');
        $data            = $this->zendRequest->getRawBody();
        $data = (array)json_decode($data);
        $device = $this->_objectManager->get('Simi\Simipushnotif\Model\Device');
        if (!$data['endpoint']) {
            throw new \Exception(__('No Endpoint Sent'), 4);
        }

        try {
            if (!$device->load($data['endpoint'], 'endpoint')->getId()) {
                $user_agent = '';
                if ($_SERVER["HTTP_USER_AGENT"]) {
                    $user_agent = $_SERVER["HTTP_USER_AGENT"];
                }
                $ip = $_SERVER['REMOTE_ADDR'];
                $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
                if($_SERVER['SERVER_NAME'] == 'localhost'){
                    $details->city = 'localhost';
                    $details->country = 'localhost';
                }
                $date = date('Y-m-d H:i:s');
                $endpoint = $data['endpoint'];
                $number = strrpos($data['endpoint'], '/');
                $endpoint_key = substr($data['endpoint'], $number + 1);
                $device->setUserAgent($user_agent)
                    ->setEndpoint($endpoint)
                    ->setEndpointKey($endpoint_key)
                    ->setData('p256dh_key', $data['keys']->p256dh)
                    ->setAuthKey($data['keys']->auth)
                    ->setCreatedAt($date)
                    ->setCity($details->city)
                    ->setCountry($details->country)
                    ->save();
            }
            $this->getResponse()->setHeader('Content-type', 'application/json', true);
            $this->getResponse()->setBody(json_encode($device->getData()));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 4);
        }
    }

    public function getMediaUrl($media_path)
    {
        return $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . $media_path;
    }
}
