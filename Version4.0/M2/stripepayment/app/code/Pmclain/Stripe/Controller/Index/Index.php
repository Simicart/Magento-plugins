<?php

//die('!!!');
//require_once(\Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\App\Filesystem\DirectoryList')->getPath(\Magento\Framework\App\Filesystem\DirectoryList::LIB_INTERNAL).'/Stripe/init.php'); 


namespace Pmclain\Stripe\Controller\Index;

require_once(\Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\App\Filesystem\DirectoryList')->getPath(\Magento\Framework\App\Filesystem\DirectoryList::LIB_INTERNAL).'/Stripe/init.php'); 

class Index extends \Magento\Framework\App\Action\Action
{
    public $data;
    public $simiObjectManager;

    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\App\Action\Context $context
    ) {
//echo $cody;die('??');
        parent::__construct($context);
        $this->simiObjectManager  = $context->getObjectManager();
        $this->directoryList = $directoryList;
        $modulePath= $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::LIB_INTERNAL).'/Stripe/init.php';
        //echo \Magento\Framework\App\Filesystem\DirectoryList::LIB_INTERNAL;die;
        //echo \Magento\Framework\App\Filesystem\DirectoryList::LIB_INTERNAL;
        //require_once($modulePath);
    }

    public function execute()
    {
        $a = $this->simiObjectManager->create('Stripe\Customer');
        var_dump(get_class($a));die;
    }
}
