<?php


namespace Simi\Hyperpay\Observer;

use Magento\Framework\Event\ObserverInterface;

class SimiconnectorModelServerInitialize implements ObserverInterface {

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
        $object = $observer->getObject();
        $data = $object->getData();
        if(isset($data['resource']) && ($data['resource'] == "hyperpays")){
            $data['module'] = "hyperpay";
            $object->setData($data);
        }
    }

}
