<?php

class Simi_Simiapicache_Model_Observer
{

    public function controllerActionPredispatch($observer)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET')
            return;

        if (!Mage::getStoreConfig('simiapicache/apicache/enable'))
            return;

        if (Mage::getSingleton('customer/session')->isLoggedIn())
            return;
        
        $uri = $_SERVER['REQUEST_URI'];
//        zend_debug::dump($uri);
        $fileName = $uri.Mage::app()->getStore()->getCurrentCurrencyCode().Mage::app()->getStore()->getId();
        $filePath = Mage::getBaseDir('media') . DS . 'simiapicache' . DS . 'simiapi_json';
        if(strpos($uri,'/home') !== false){
            $filePath = $filePath . DS . 'home_api';
        }elseif (strpos($uri,'/products') !== false){
            $params = $observer->getControllerAction()->getRequest()->getParams();
            if(isset($params['products']) && $params['products']){
                $filePath = $filePath . DS . 'products_detail';
            }else{
                $filePath = $filePath . DS . 'products_list';
            }
        }elseif (strpos($uri,'/urldicts/detail') !== false){
            $filePath = $filePath . DS . 'urldicts';
        }
        else{
            $filePath = $filePath . DS . 'other_api';
        }
        $filePath = $filePath . DS . md5($fileName) . ".json";
        if (file_exists($filePath)) {
            $apiResult = file_get_contents($filePath);
            if ($apiResult) {
                ob_start('ob_gzhandler');
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

//        zend_debug::dump($observer->getObject());die;
        $result = $observer->getObject()->getData();
        $current_api = Mage::helper('core/url')->getCurrentUrl();
        $result['api_cache'] = $current_api;
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

        if ((strpos($uri, 'storeviews') !== false) && 
            (strpos($uri, 'storeviews/default') === false) &&
                (strpos($uri, 'storeviews/'.Mage::app()->getStore()->getId()) === false))
            return $this;

        $path =  Mage::getBaseDir('media') . DS . 'simiapicache' . DS . 'simiapi_json';
        if (!is_dir($path)) {
            try {
                mkdir($path, 0777, true);
            } catch (\Exception $e) {
            }
        }
        if(strpos($uri,'/home') !== false){
            $path = Mage::getBaseDir('media') . DS . 'simiapicache' . DS . 'simiapi_json' . DS . 'home_api';
            if (!is_dir($path)) {
                try {
                    mkdir($path, 0777, true);
                } catch (\Exception $e) {
                }
            }
        }elseif (strpos($uri,'/products') !== false){
            $path = Mage::getBaseDir('media') . DS . 'simiapicache' . DS . 'simiapi_json' . DS . 'products_list';
            // if(strpos($uri, '/products/'))
            $params = $observer->getObject()->getRequest()->getParams();
            if(isset($params['products']) && $params['products']){
                $path = Mage::getBaseDir('media') . DS . 'simiapicache' . DS . 'simiapi_json' . DS . 'products_detail';
            }
            if (!is_dir($path)) {
                try {
                    mkdir($path, 0777, true);
                } catch (\Exception $e) {
                }
            }
        }elseif (strpos($uri,'/urldicts/detail') !== false){
            $path = Mage::getBaseDir('media') . DS . 'simiapicache' . DS . 'simiapi_json' . DS . 'urldicts';
            if (!is_dir($path)) {
                try {
                    mkdir($path, 0777, true);
                } catch (\Exception $e) {
                }
            }
        }
        else{
            $path = Mage::getBaseDir('media') . DS . 'simiapicache' . DS . 'simiapi_json' . DS . 'other_api';
            if (!is_dir($path)) {
                try {
                    mkdir($path, 0777, true);
                } catch (\Exception $e) {
                }
            }
        }
        $fileName = $uri.Mage::app()->getStore()->getCurrentCurrencyCode().Mage::app()->getStore()->getId();
        $filePath = $path . DS . md5($fileName) . ".json";

        $data_json = json_encode($result);
        file_put_contents($filePath, $data_json);
    }

    public function flushcache($observer) {
        if(!Mage::getStoreConfig('simiapicache/apicache/auto_flush')){
            return $this;
        }
        $passedModels = [
            'Mage_Log_Model_Visitor',
            'Mage_Sales_Model_Quote',
            'Mage_Sales_Model_Quote_Item_Option',
            'Mage_Sales_Model_Quote_Item',
            'Mage_Sales_Model_Quote_Address',
            'Mage_Sales_Model_Quote_Address_Rate',
            'Mage_Sales_Model_Quote_Payment',
            'Idev_OneStepCheckout_Model_Sales_Quote',
            'Mage_CatalogInventory_Model_Stock_Item',
            'Mage_Index_Model_Event',
            'Mage_Reports_Model_Product_Index_Viewed',
            'Mage_Reports_Model_Event',
            'Mage_Eav_Model_Entity_Store',
            'Mage_Customer_Model_Address',
            'Mage_Customer_Model_Customer',
            'Mage_Sales_Model_Order_Address',
            'Mage_Sales_Model_Order_Item',
            'Mage_Sales_Model_Order_Payment',
            'AdjustWare_Notification_Model_Rewrite_Sales_Order',
            'Mage_Tax_Model_Sales_Order_Tax',
            'Simtech_Searchanise_Model_Queue',
            'Mage_SalesRule_Model_Coupon',
            'Mage_SalesRule_Model_Rule',
            'Mage_SalesRule_Model_Rule_Customer',
            'Mage_Sales_Model_Order_Status_History',
            'Mage_Sales_Model_Order_Payment_Transaction',
            'Mage_Sales_Model_Order_Invoice',
            'Mage_Sales_Model_Order_Invoice_Item',
            'Mage_CatalogSearch_Model_Query',
            'Ebizmarts_MailChimp_Model_Subscriber',
            'Mage_Tax_Model_Sales_Order_Tax_Item'
        ];
        if (!$observer->getObject())
            return $this;
        $modelClass = get_class($observer->getObject());
        if($modelClass == 'Mage_Core_Model_Config_Data') return $this;
        foreach ($passedModels as $passedModel) {
            if ($passedModel == $modelClass){
                return $this;
            }
        }


        Mage::log('Saved Model '.$modelClass, null, 'savedmodels.log');
        Mage::helper('simiapicache')->flushCache();
    }

    public function ProductSaveAfter($observer){
        $product = $observer->getProduct();
        $id = $product->getId();
        Mage::helper('simiapicache')->removeOnList($id,'urldicts',false);
        Mage::helper('simiapicache')->removeOnList($id,'products_detail',false);
        Mage::helper('simiapicache')->removeOnList($id,'products_list');
    }
}