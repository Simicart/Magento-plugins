<?php

/**
 * Created by PhpStorm.
 * User: trueplus
 * Date: 4/19/16
 * Time: 08:52
 */

namespace Simi\Paypalmobile\Observer;

use Magento\Framework\Event\ObserverInterface;

class SimiconnectorModelServerInitialize implements ObserverInterface {

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
        $data = $object->getData();
        if(isset($data['resource']) && ($data['resource'] == "paypalmobiles")){
            $data['module'] = "paypalmobile";
            $object->setData($data);
        }
    }

}
