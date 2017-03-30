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

class NewsletterSubscriberSaveAfter implements ObserverInterface
{

    private $simiObjectManager;

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
        $event = $observer->getEvent();
        $subscriber = $event->getDataObject();
        $data = $subscriber->getData();
        $email = $data['subscriber_email'];
        $isEnable = $this->simiObjectManager->get('Simi\Simipromoteapp\Helper\Email')->isEnable();
        $statusChange = $subscriber->getIsStatusChanged();
        if($isEnable) {
                if ($data['subscriber_status'] == "1" && $statusChange == true) {
               //code to handle if customer is just subscribed...
                $name = explode('@',$email);
                $data = array(
                    'name' => $name[0],
                    'email' => $email,
                    'is_subscriber' => Status::SUBSCRIBER_YES
                );
                $this->simiObjectManager->get('Simi\Simipromoteapp\Helper\Email')
                        ->sendEmail($data, Status::TYPE_SUBSCRIBER);
            }
        }
    }
}
