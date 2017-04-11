<?php

/**
 * Created by PhpStorm.
 * User: trueplus
 * Date: 4/19/16
 * Time: 08:52
 */

namespace Simi\Paypalmobile\Observer;

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
        if (isset($data['payment_method']) && $data['payment_method'] == "paypal_mobile") {
            $order = $this->simiObjectManager
                    ->get('\Magento\Sales\Api\Data\OrderInterface')->loadByIncrementId($data['invoice_number']);
            if ($order->getState() == \Magento\Sales\Model\Order::STATE_PROCESSING) {
                $order->setStatus(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
                $order->save();
            }
        }
        $orderObject->order_placed_info = $data;
    }
}
