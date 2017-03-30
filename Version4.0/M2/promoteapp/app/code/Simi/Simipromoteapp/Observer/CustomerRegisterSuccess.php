<?php

/**
 * Created by PhpStorm.
 * User: trueplus
 * Date: 4/19/16
 * Time: 08:52
 */

namespace Simi\Simipromoteapp\Observer;

use Simi\Simipromoteapp\Model\Status;
use Magento\Framework\Event\ObserverInterface;

class CustomerRegisterSuccess implements ObserverInterface
{

    private $simiObjectManager;
    public $new_added_product_sku = '';

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {
   
        $this->simiObjectManager = $simiObjectManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getCustomer();
        $isEnable = $this->simiObjectManager->get('Simi\Simipromoteapp\Helper\Email')->isEnable();
        if($isEnable && $customer->getId()){
            $customer = $this->simiObjectManager->get('Magento\Customer\Model\Customer')->load($customer->getId());
            // send email
            $data = array(
                'name' => $customer->getFirstname(),
                'email' => $customer->getEmail(),
                'is_subscriber' => Status::SUBSCRIBER_NO
            );
            $this->simiObjectManager->get('Simi\Simipromoteapp\Helper\Email')->sendEmail($data, Status::TYPE_REGISTER);
        }
    }
}
