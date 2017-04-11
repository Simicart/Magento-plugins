<?php

/**
 *
 * Copyright © 2016 Simicommerce. All rights reserved.
 */

namespace Simi\Simicheckoutcom\Controller\Index;

class Paymentrestv22 extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
        $simiObjectManager = $this->_objectManager;
        $checkoutcomHelper = $simiObjectManager->get('Simi\Simicheckoutcom\Helper\Data');
        $paymentToken = $this->getRequest()->getParam('payment_token');
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $simiObjectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($orderId);
        if ($checkoutcomHelper->getStoreConfig("payment/simicheckoutcom/is_sandbox")) {
            echo "<script src=\"https://sandbox.checkout.com/js/checkout.js\"></script>";
        } else {
            echo "<script src=\"https://cdn.checkout.com/js/checkout.js\"></script>";
        }
        echo "
        <form method=\"POST\" class=\"payment-form\">
            <script>
                Checkout.render({
                    debugMode: false,
                    publicKey: '" . $checkoutcomHelper->getStoreConfig("payment/simicheckoutcom/public_key") . "',
                    paymentToken: '" . $paymentToken . "',
                    customerEmail: '" . $order->getData('customer_email') . "',
                    customerName: '" . $order->getData('customer_firstname') . ' ' . $order->getData('customer_middlename') . ' ' . $order->getData('customer_lastname') . "',
                    value: " . floatval($order->getData('grand_total')) * 100 . ",
                    currency: '" . $order->getData('order_currency_code') . "',
                    widgetContainerSelector: '.payment-form',
                    widgetColor: '#333',
                    themeColor: '#3075dd',
                    buttonColor:'#51c470',
                    logoUrl: \"http://www.merchant.com/images/logo.png\",
                });
            </script>
        </form>
        ";
        exit();
        exit();
    }
}
