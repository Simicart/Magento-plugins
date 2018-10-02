<?php

/**
 * Created by PhpStorm.
 * User: trueplus
 * Date: 4/19/16
 * Time: 08:52
 */

namespace Simi\Simibraintree\Observer;

use Magento\Framework\Event\ObserverInterface;

class PaymentMethodIsActive implements ObserverInterface {

    public $simiObjectManager;

    public function __construct() {
        $this->simiObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $method = $observer['method_instance'];
        if ($method->getCode() == 'simibraintree') {
            if (!strpos($this->simiObjectManager->get('\Magento\Framework\Url')->getCurrentUrl(), 'simiconnector')) {
                $result = $observer->getEvent()->getResult();
                $result->setData('is_available', false);
            }
        }
    }

}
