<?php

/**
 * Created by PhpStorm.
 * User: trueplus
 * Date: 4/19/16
 * Time: 08:52
 */

namespace Simi\Simicustompayment\Observer;

use Magento\Framework\Event\ObserverInterface;

class SimiconnectorAfterPlaceOrder implements ObserverInterface {

    public $simiObjectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {
        $this->simiObjectManager = $simiObjectManager;
    }
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $orderObject = $observer->getObject();
        $data = $orderObject->order_placed_info;
        if (isset($data['payment_method']) && $data['payment_method'] == "simibraintree") {
            $orderObject = $observer->getObject();
            $data = $orderObject->order_placed_info;
            $data['url_action'] = $this->getOrderPlaceRedirectUrl($data['invoice_number']);
            $orderObject->order_placed_info = $data;
        }
    }

    public function getOrderPlaceRedirectUrl($order_id) {

        $checkoutSession = $this->simiObjectManager->create('Magento\Checkout\Model\Session');
        return $this->simiObjectManager->get('Magento\Framework\UrlInterface')
            ->getUrl('simicustompayment/index/placement', array('_secure' => true,
            'OrderID' => base64_encode($order_id),
            'LastRealOrderId' => base64_encode($checkoutSession->getLastRealOrderId())
        ));
    }

}
