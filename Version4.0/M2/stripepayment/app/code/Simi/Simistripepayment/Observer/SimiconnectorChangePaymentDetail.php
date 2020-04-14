<?php


namespace Simi\Simistripepayment\Observer;

use Magento\Framework\Event\ObserverInterface;

class SimiconnectorChangePaymentDetail implements ObserverInterface
{
    private $simiObjectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {
        $this->simiObjectManager = $simiObjectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $object = $observer->getObject();
        if(strtoupper($object->detail['payment_method']) == 'PMCLAIN_STRIPE') {
            $StripeConfig = $this->simiObjectManager->create('Pmclain\Stripe\Model\Ui\ConfigProvider');
            $object->detail['stripe_public_key'] = $StripeConfig->getPublishableKey();
            $object->detail['stripe_3d_secure'] = $StripeConfig->get3dSecure() == 1;
        }
    }
}
