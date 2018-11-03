<?php
namespace Simi\Simibraintree\Controller\Index;

use Magento\Backend\App\Action\Context;
class Redirect extends  \Magento\Framework\App\Action\Action{
    public $simiObjectManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context
    ) {

        parent::__construct($context);
    }

    public function execute()
    {
    	$orderId = base64_decode($this->getRequest()->getParam('OrderId'));
        $block = $this->_objectManager->create('Simi\Simibraintree\Block\Form');
        $block->setOrderId($orderId);
        $html = $block->toHtml();
        return $this->getResponse()->setBody($html);
    }

}
