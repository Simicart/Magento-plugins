<?php

/**
 * Created by PhpStorm.
 * User: trueplus
 * Date: 4/19/16
 * Time: 08:52
 */

namespace Simi\Paypalmobile\Observer;

use Magento\Framework\DataObject as Object;
use Magento\Framework\Event\ObserverInterface;

class SimiconnectorAddPaymentMethod implements ObserverInterface {

    private $_objectManager;

    public function __construct() {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $object = $observer->getObject();           
        $object->addPaymentMethod('paypal_mobile', 2);
        return;
    }

}
