<?php

/**
 * Created by PhpStorm.
 * User: trueplus
 * Date: 4/19/16
 * Time: 08:52
 */

namespace Simi\Simicheckoutcom\Observer;

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
        if (isset($data['payment_method']) && $data['payment_method'] == "simicheckoutcom") {
            $data['redirect_url'] = $this->simiObjectManager->get('Magento\Framework\UrlInterface')
                    ->getUrl('simicheckoutcom/index/StartCheckoutcom').'?order_id='.$data['invoice_number'];
            $data['success_url'] = $this->_getConfig("payment/simicheckoutcom/success_url");
            
            $orderStatus = $this->_getConfig('payment/simicheckoutcom/order_status');
            $order = $this->simiObjectManager
                    ->get('\Magento\Sales\Api\Data\OrderInterface')->loadByIncrementId($data['invoice_number']);
            if ($orderStatus && $order->getState() == \Magento\Sales\Model\Order::STATE_PROCESSING) {
                $order->setStatus($orderStatus);
                $order->save();
            }
        }
        $orderObject->order_placed_info = $data;
    }
    
    protected function _getConfig($config)
    {
        return $this->simiObjectManager
                ->get('Magento\Framework\App\Config\ScopeConfigInterface')
                ->getValue($config);
    }

}
