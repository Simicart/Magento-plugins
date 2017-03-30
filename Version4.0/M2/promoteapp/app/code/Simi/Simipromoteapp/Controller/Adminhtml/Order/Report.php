<?php
/**
 *
 * Copyright © 2017 Simicommerce. All rights reserved.
 */
namespace Simi\Simipromoteapp\Controller\Adminhtml\Order;

use Simi\Simipromoteapp\Model\Status;

class Report extends \Magento\Backend\App\Action
{
    
    public $simiObjectManager;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->simiObjectManager  = $context->getObjectManager();
        parent::__construct($context);
    }
    
    
    public function execute()
    {
        $from_date = $this->getRequest()->getParam('from_date');
        $to_date = $this->getRequest()->getParam('to_date');

        $data = array(
                'from_date' => $from_date,
                'to_date' => $to_date,
        );

        $output = $this->simiObjectManager->get('Simi\Simipromoteapp\Helper\Order')->getInfo($data);
        $json = json_encode($output);

        $this->getResponse()
                ->clearHeaders()
                ->setHeader('Content-Type', 'application/json')
                ->setBody($json);
    }
}
