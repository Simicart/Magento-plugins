<?php


namespace Simi\Simibarclays\Observer;

use Magento\Framework\Event\ObserverInterface;

class PaymentMethodIsActive implements ObserverInterface {

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
        $method = $observer['method_instance'];
        if ($method->getCode() == 'simibarclays') {
            $result = $observer->getEvent()->getResult();
            $result->setData('is_available', true);
            if (strpos($this->simiObjectManager->get('\Magento\Framework\Url')->getCurrentUrl(), 'simiconnector') === false) {
                $result = $observer->getEvent()->getResult();
                $result->setData('is_available', false);
            }
        }
    }

}
