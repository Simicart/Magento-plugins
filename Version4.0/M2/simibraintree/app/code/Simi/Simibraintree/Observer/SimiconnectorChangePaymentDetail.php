<?php


namespace Simi\Simibraintree\Observer;


use Magento\Framework\Event\ObserverInterface;

class SimiconnectorChangePaymentDetail implements ObserverInterface {

    public $simiObjectManager;

    public function __construct() {
        $this->simiObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $object = $observer->getObject();
        if($object->detail['payment_method'] == 'SIMIBRAINTREE' && $object->detail['p_method_selected']) {
            $object->detail['token'] = $this->simiObjectManager->create('\Simi\Simibraintree\Helper\Data')->getTokenKey();
        }
    }

}