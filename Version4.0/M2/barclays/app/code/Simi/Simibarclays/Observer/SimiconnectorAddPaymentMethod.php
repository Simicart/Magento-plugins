<?php

/**
 * Created by PhpStorm.
 * User: trueplus
 * Date: 4/19/16
 * Time: 08:52
 */

namespace Simi\Simibarclays\Observer;

use Magento\Framework\DataObject as Object;
use Magento\Framework\Event\ObserverInterface;

class SimiconnectorAddPaymentMethod implements ObserverInterface {

    public $simiObjectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {
   
        $this->simiObjectManager = $simiObjectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $object = $observer->getObject();           
        $object->addPaymentMethod('simibarclays', 3);
        return;
    }

}
