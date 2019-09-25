<?php
/**
 * Created by PhpStorm.
 * User: scott
 * Date: 9/26/18
 * Time: 2:34 PM
 */

namespace Simi\Simibraintree\Block;


class Info extends \Magento\Payment\Block\Info
{
	public $simiObjectManager;

	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->simiObjectManager = $simiObjectManager;
    }


    protected function _prepareSpecificInformation($transport = null)
    {
        $order_id = $this->getRequest()->getParam('order_id');
        $transport = parent::_prepareSpecificInformation($transport);
        $data = [];
        $transactionInfo = $this->simiObjectManager->create('Simi\Simibraintree\Model\Simibraintree')
                ->getCollection()
                ->addFieldToFilter('order_id',$order_id)
                ->getLastItem();
                ;
        $data[(string)__('Transaction Id')] = $transactionInfo->getTransactionId();
        if($transactionInfo->getStatus()){
            $data[(string)__('Status')] = str_replace("_", " ", $transactionInfo->getStatus());
        }
        $additionalData = $transactionInfo->getAdditionalData();
        if($additionalData !=''){
        	$customData = json_decode($additionalData,true);
            $data[(string)__('Credit Card Type')] = $customData['cardType'];
            $data[(string)__('Credit Card Number')] = sprintf('xxxx-%s', $customData['ccLast4']);
        }


        return $transport->setData(array_merge($data, $transport->getData()));
    }
}