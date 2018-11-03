<?php

namespace Simi\Simifbconnect\Observer;
/**
* 
*/
use Magento\Framework\Event\ObserverInterface;
class GetStoreviewDetailAfter implements ObserverInterface
{

    private $simiObjectManager;
    private $helper;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Simi\Simifbconnect\Helper\Data $helper
    ) {
   
        $this->simiObjectManager = $simiObjectManager;
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer){
    	if($this->helper->getStoreConfig('simifbconnect/general/enable')){
    		$observerObject = $observer->getObject();
        	$data = $observerObject->getData();
        	$obj = $observer['object'];
            $info = $obj->storeviewInfo;
            	$info['facebook_connect'] = [
            		'invite_link' =>$this->helper->getStoreConfig('simifbconnect/fbappinvite/invite_link'),
            		'image_description_link'=>$this->helper->getStoreConfig('simifbconnect/fbappinvite/image_description_link')
            	];
            $obj->storeviewInfo = $info;
    	}
    }

}