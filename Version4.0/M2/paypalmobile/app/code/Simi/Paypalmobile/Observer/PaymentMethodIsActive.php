<?php

/**
 * Created by PhpStorm.
 * User: trueplus
 * Date: 4/19/16
 * Time: 08:52
 */

namespace Simi\Paypalmobile\Observer;

use Magento\Framework\Event\ObserverInterface;

class PaymentMethodIsActive implements ObserverInterface {

    private $_objectManager;

    public function __construct() {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $result = $observer['result'];
        $method = $observer['method_instance'];
        if ($method->getCode() == 'paypal_mobile') {
            if (!strpos($this->_objectManager->get('\Magento\Framework\Url')->getCurrentUrl(), 'simiconnector')) {
                $result->isAvailable = false;
            }
        }
    }

}
