<?php

/**
 * Created by PhpStorm.
 * User: trueplus
 * Date: 4/19/16
 * Time: 08:52
 */

namespace Simi\Simicheckoutcom\Observer;

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
        if(isset($data['resource']) && ($data['resource'] == "checkoutcomapis")){
            $data['module'] = "simicheckoutcom";
            $object->setData($data);
        }
    }

}
