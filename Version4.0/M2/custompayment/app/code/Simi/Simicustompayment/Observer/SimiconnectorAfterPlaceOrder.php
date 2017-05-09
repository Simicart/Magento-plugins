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
        if (isset($data['payment_method']) && $data['payment_method'] == "simicustompayment") {
            $orderObject = $observer->getObject();
            $data = $orderObject->order_placed_info;
            $data['url_action'] = $this->getOrderPlaceRedirectUrl($data['invoice_number']);
            $orderObject->order_placed_info = $data;
        }
    }

    public function getOrderPlaceRedirectUrl($order_id) {

        $checkoutSession = $this->simiObjectManager->create('Magento\Checkout\Model\Session');
        return $this->simiObjectManager->get('Magento\Framework\UrlInterface')
            ->getUrl('simicustompayment/api/placement', array('_secure' => true,
            'OrderID' => base64_encode($order_id),
            'Amount' => base64_encode($checkoutSession->getAmount()),
            'CurrencyCode' => base64_encode($checkoutSession->getCurrencycode()),
            'TransactionType' => base64_encode($checkoutSession->getTransactiontype()),
            'TransactionDateTime' => base64_encode($checkoutSession->getTransactiondatetime()),
            'CallbackURL' => base64_encode($checkoutSession->getCallbackurl()),
            'OrderDescription' => base64_encode($checkoutSession->getOrderdescription()),
            'CustomerName' => base64_encode($checkoutSession->getCustomername()),
            'Address1' => base64_encode($checkoutSession->getAddress1()),
            'Address2' => base64_encode($checkoutSession->getAddress2()),
            'Address3' => base64_encode($checkoutSession->getAddress3()),
            'Address4' => base64_encode($checkoutSession->getAddress4()),
            'City' => base64_encode($checkoutSession->getCity()),
            'State' => base64_encode($checkoutSession->getState()),
            'PostCode' => base64_encode($checkoutSession->getPostcode()),
            'LastRealOrderId' => base64_encode($checkoutSession->getLastRealOrderId())
        ));
    }

}
