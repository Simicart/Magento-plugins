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
        $fileName = $uri.Mage::app()->getStore()->getCurrentCurrencyCode().Mage::app()->getStore()->getId();
        $filePath = Mage::getBaseDir('media') . DS . 'simiapicache' . DS . 'simiapi_json' . DS . md5($fileName) . ".json";
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
        $fileName = $uri.Mage::app()->getStore()->getCurrentCurrencyCode().Mage::app()->getStore()->getId();
        $filePath = Mage::getBaseDir('media') . DS . 'simiapicache' . DS . 'simiapi_json' . DS . md5($fileName) . ".json";

        $data_json = json_encode($result);
        file_put_contents($filePath, $data_json);
    }

    public function flushcache($observer) {
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
        foreach ($passedModels as $passedModel) {
            if ($passedModel == $modelClass){
                return $this;
            }
        }


        Mage::log('Saved Model '.$modelClass, null, 'savedmodels.log');
        Mage::helper('simiapicache')->flushCache();
    }
}