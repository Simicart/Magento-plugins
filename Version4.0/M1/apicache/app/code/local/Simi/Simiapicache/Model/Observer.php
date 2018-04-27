<?php

class Simi_Simiapicache_Model_Observer
{

    public function controllerActionPredispatch($observer)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET')
            return;

        if (!Mage::getStoreConfig('simiapicache/apicache/enable'))
            return;

        $uri = $_SERVER['REQUEST_URI'];
        $filePath = Mage::getBaseDir('var') . DS . 'cache' . DS . 'simiapi_json' . DS . md5($uri) . ".json";
        if (file_exists($filePath)) {
            $apiResult = file_get_contents($filePath);
            if ($apiResult) {
                header('Simi-Api-Cache: HIT');
                header('Content-Type: application/json');
                echo $apiResult;
                exit();
            }
        }
    }
    
    public function apijsonencoding($observer) {
        if (!$observer->getObject())
            return;

        if (!Mage::getStoreConfig('simiapicache/apicache/enable'))
            return;
       
        $result = $observer->getObject()->getData();
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
        
        if ($excludedPaths = Mage::getStoreConfig('simiapicache/general/excluded_paths')) {
            $excludedPaths = explode(',', str_replace(' ', '', $excludedPaths));
            $whitelistApis = array_merge($whitelistApis, $excludedPaths);
        }
        
        foreach ($whitelistApis as $whitelistApi) {
            if (($whitelistApi != '') && (strpos($uri, $whitelistApi) !== false))
                return $this;
        }

        if ((strpos($uri, 'storeviews') !== false) && (strpos($uri, 'storeviews/default') === false))
            return $this;

        $path =  Mage::getBaseDir('var') . DS . 'cache' . DS . 'simiapi_json';
        if (!is_dir($path)) {
            try {
                mkdir($path, 0777, true);
            } catch (\Exception $e) {
            }
        }
        
        $filePath = Mage::getBaseDir('var') . DS . 'cache' . DS . 'simiapi_json' . DS . md5($uri) . ".json";
        
        $data_json = json_encode($result);
        file_put_contents($filePath, $data_json);
    }
    
    public function flushcache($observer) {
        $passedModels = [
            'Mage_Log_Model_Visitor',
            'Mage_Sales_Model_Quote',
            'Mage_Sales_Model_Quote_Address',
            'Mage_Sales_Model_Quote_Address_Rate',
            'Mage_Sales_Model_Quote_Payment',
        ];
        if (!$observer->getObject())
            return $this;
        $modelClass = get_class($observer->getObject());
        foreach ($passedModels as $passedModel) {
            if ($passedModel == $modelClass){
                return $this;
            }
        }
        Mage::helper('simiapicache')->flushCache();
    }
}