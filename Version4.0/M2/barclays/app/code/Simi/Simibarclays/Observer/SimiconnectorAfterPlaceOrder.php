<?php

namespace Simi\Simibarclays\Observer;

use Magento\Framework\Event\ObserverInterface;

class SimiconnectorAfterPlaceOrder implements ObserverInterface {

    public $simiObjectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {
        $this->simiObjectManager = $simiObjectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $orderObject = $observer->getObject();
        $data = $orderObject->order_placed_info;
        if (isset($data['payment_method']) && $data['payment_method'] == "simibarclays") {
            $orderObject = $observer->getObject();
            $data = $orderObject->order_placed_info;
            $data['url_action'] = $this->getOrderPlaceRedirectUrl($data['invoice_number']);
            $orderObject->order_placed_info = $data;
        }
    }

    public function getOrderPlaceRedirectUrl($invoiceNumber) {
        return $this->simiObjectManager->get('Magento\Framework\UrlInterface')
            ->getUrl('simibarclays/index/placement', array('_secure' => true,
            'OrderId' => $invoiceNumber
        ));
    }

}
