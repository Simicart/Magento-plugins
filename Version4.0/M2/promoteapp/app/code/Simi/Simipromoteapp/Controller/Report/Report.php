<?php
/**
 *
 * Copyright © 2017 Simicommerce. All rights reserved.
 */
namespace Simi\Simipromoteapp\Controller\Report;

use Simi\Simipromoteapp\Model\Status;

class Report extends \Magento\Framework\App\Action\Action
{
    
    public $simiObjectManager;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->simiObjectManager  = $context->getObjectManager();
        parent::__construct($context);
    }
    
    
    public function execute()
    {
        $customer_email = $this->getRequest()->getParam('email');
        $template_id = $this->getRequest()->getParam('template_id');
        // get report object
        if($customer_email != null && $template_id != null){
            $report = $this->simiObjectManager->get('Simi\Simipromoteapp\Model\Simipromoteapp')->getCollection()
                        ->addFieldToFilter('template_id',$template_id)
                        ->addFieldToFilter('customer_email',$customer_email)
                        ->getFirstItem();
                if(!$report->getId()){
                    $report = $this->simiObjectManager->create('Simi\Simipromoteapp\Model\Simipromoteapp')
                            ->setData('template_id',$template_id)
                            ->setData('customer_email',$customer_email);
                }
                $report->setData('is_open',Status::STATUS_ENABLED);
                $report->setData('update_time',$this->simiObjectManager
                        ->get('\Magento\Framework\Stdlib\DateTime\DateTimeFactory')->create()->gmtDate());
                $report->save();
        }
    }
}
