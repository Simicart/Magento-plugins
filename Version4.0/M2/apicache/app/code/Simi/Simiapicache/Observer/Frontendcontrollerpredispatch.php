<?php

namespace Simi\Simiapicache\Observer;

use Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\ObjectManagerInterface as ObjectManager;
use Magento\Framework\App\Filesystem\DirectoryList;

class Frontendcontrollerpredispatch implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface as ObjectManager
     */
    private $simiObjectManager;

    public function __construct(ObjectManager $simiObjectManager)
    {
        $this->simiObjectManager = $simiObjectManager;
    }

    /**
     * Add site map data to api get storeview
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET')
            return;
        
        if (!$this->simiObjectManager
            ->get('\Magento\Framework\App\Config\ScopeConfigInterface')
            ->getValue('simiapicache/general/enable'))
            return;
        
        $customerSession = $this->simiObjectManager->get('Magento\Customer\Model\Session');
        if($customerSession->isLoggedIn())
            return;
        
        $uri = $_SERVER['REQUEST_URI'];

        if (strpos($uri, 'simiconnector') === false)
            return;

        $dirList = $this->simiObjectManager->get('Magento\Framework\App\Filesystem\DirectoryList');
        
        $this->storeManager = $this->simiObjectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $storeId = $this->storeManager->getStore()->getId();
        $currencyCode   = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $fileName = $uri.$currencyCode.$storeId;
        
        $filePath = $dirList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'Simiapicache'
            . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR . md5($fileName) .".json";
        if (file_exists($filePath)) {
            $apiResult = file_get_contents($filePath);
            if ($apiResult) {
                header('Simi-Api-Cache: HIT');
                header('Content-Type: application/json');
                ob_start('ob_gzhandler');
                echo $apiResult;
                exit();
                //return $observer->getData('controller_action')->getResponse()->setBody($apiResult);
            }
        }
    }
}
