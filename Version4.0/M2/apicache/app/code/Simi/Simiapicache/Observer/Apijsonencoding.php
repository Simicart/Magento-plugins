<?php

namespace Simi\Simiapicache\Observer;

use Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\ObjectManagerInterface as ObjectManager;

use Magento\Framework\App\Filesystem\DirectoryList;

class Apijsonencoding implements ObserverInterface
{
    private $simiObjectManager;

    public function __construct(ObjectManager $simiObjectManager)
    {
        $this->simiObjectManager = $simiObjectManager;
    }
    
    public function execute(Observer $observer)
    {
        if (!$observer->getObject())
            return;
        
        if (!$this->simiObjectManager
            ->get('\Magento\Framework\App\Config\ScopeConfigInterface')
            ->getValue('simiapicache/general/enable'))
            return;
        
        $result = $observer->getObject()->data;
        if (!$result || (isset($result['errors'])))
            return;
        
        $uri = $_SERVER['REQUEST_URI'];
        $whitelistApis = [
            'quoteitems', 
            'orders', 
            'customers',
            'customizepayments',
            'addresses',
        ];
        if ($excludedPaths = $this->simiObjectManager
            ->get('\Magento\Framework\App\Config\ScopeConfigInterface')
            ->getValue('simiapicache/general/excluded_paths')) {
            $excludedPaths = explode(',', str_replace(' ', '', $excludedPaths));
            $whitelistApis = array_merge($whitelistApis, $excludedPaths);
        }
        
        foreach ($whitelistApis as $whitelistApi) {
            if (($whitelistApi != '') && (strpos($uri, $whitelistApi) !== false))
                return $this;
        }

        $this->storeManager = $this->simiObjectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $storeId = $this->storeManager->getStore()->getId();
        
        if ((strpos($uri, 'storeviews') !== false) &&
            (strpos($uri, 'storeviews/default') === false) &&
            (strpos($uri, 'storeviews/'.$storeId) === false)
        )
            return $this;
        
        $dirList = $this->simiObjectManager->get('Magento\Framework\App\Filesystem\DirectoryList');
        $path = $dirList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'Simiapicache'
            . DIRECTORY_SEPARATOR . 'json';
        if (!is_dir($path)) {
            try {
                mkdir($path, 0777, true);
            } catch (\Exception $e) {
            }
        }
        $currencyCode   = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $fileName = $uri.$currencyCode.$storeId;

        $filePath = $dirList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'Simiapicache'
            . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR . md5($fileName) .".json";
        
        $data_json = json_encode($result);
        file_put_contents($filePath, $data_json);
    }
}
