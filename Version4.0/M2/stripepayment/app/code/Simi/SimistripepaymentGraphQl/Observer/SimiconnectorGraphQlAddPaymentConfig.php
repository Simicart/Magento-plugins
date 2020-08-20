<?php

namespace Simi\SimistripepaymentGraphQl\Observer;

use Magento\Framework\Event\ObserverInterface;

class SimiconnectorGraphQlAddPaymentConfig implements ObserverInterface {

    public $configProvider;

    public function __construct(
        \Pmclain\Stripe\Model\Ui\ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $object = $observer->getObject();
        if ($object->configArray && is_array($object->configArray)) {
            $object->configArray['simi_stripe_config'] = array(
                'stripe_public_key' => $this->configProvider->getPublishableKey(),
                'stripe_3d_secure' => ($this->configProvider->get3dSecure() == 1)
            );
        }
    }

}
