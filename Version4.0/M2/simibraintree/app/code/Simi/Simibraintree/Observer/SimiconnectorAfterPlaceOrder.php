<?php

/**
 * Created by PhpStorm.
 * User: trueplus
 * Date: 4/19/16
 * Time: 08:52
 */

namespace Simi\Simibraintree\Observer;

use Magento\Framework\Event\ObserverInterface;

class SimiconnectorAfterPlaceOrder implements ObserverInterface
{
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
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderObject = $observer->getObject();
        $orderPlaceInfo = $orderObject->order_placed_info;
        if (isset($orderPlaceInfo['payment_method']) && $orderPlaceInfo['payment_method'] == "simibraintree") {
            $invoiceNumber = $orderPlaceInfo['invoice_number'];

            $data       = $orderObject->getData();
            $parameters = (array) $data['contents'];
            $nonce = $parameters['nonce'];
            $type = $parameters['type'];
            $description = $parameters['description'];

            $amount = $orderObject->_getQuote()->getGrandTotal();

            $braintreeData = json_decode(json_encode([
                'order_id' => $invoiceNumber,
                'amount' => $amount,
                'nonce' => $nonce,
                'type' => $type,
                'description' => $description
            ]), FALSE);
            $message = $this->simiObjectManager->create('Simi\Simibraintree\Model\Simibraintree')
                                    ->updateBraintreePayment($braintreeData);
            if($message) {
                $messages = array();
                $messages[] = $message;
                $orderPlaceInfo['message'] = $messages;
                $orderObject->order_placed_info = $orderPlaceInfo;
            }
        }
    }
}
