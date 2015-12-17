<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Appwishlist
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Appwishlist Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Appwishlist
 * @author      Magestore Developer
 */
class Simi_Appwishlist_Model_Observer {

    /**
     * process controller_action_predispatch event
     *
     * @return Simi_Appwishlist_Model_Observer
     */
    public function controllerActionPredispatch($observer) {
        $action = $observer->getEvent()->getControllerAction();
        return $this;
    }
    
    public function connectorConfigGetPluginsReturn($observer) 
    {
        if ($this->getConfig("enable") == 0) {
            $observerObject = $observer->getObject();
            $observerData = $observer->getObject()->getData();
            $contactPluginId = NULL;
            $plugins = array();
            foreach ($observerData['data'] as $key => $plugin) {
                if ($plugin['sku'] == 'simi_appwishlist') continue;
                $plugins[] = $plugin;
            }
            $observerData['data'] = $plugins;
            $observerObject->setData($observerData);
        }
    }

    public function connectorCatalogGetProductDetailReturn($observer) {

        try {
            $idOnWishlist = '0';
            $observerObject = $observer->getObject();
            $observerData = $observer->getObject()->getData();
            $productId = $observerData['data'][0]['product_id'];
            $product = Mage::getModel('catalog/product')->load($productId);
            $productType = $product->getTypeID();

            $skippedTypes = array('grouped', 'configurable', 'bundle');
            if ((!in_array($productType, $skippedTypes) && (!$observerData['data'][0]['options']))) {

                $wishlist = $this->_getWishlistFromCustomer();
                if ($wishlist && ($wishlist->getId())) {
                    $itemCollection = $wishlist->getItemCollection();
                    if ($itemCollection) {
                        $itemCollection = $itemCollection->getData();
                        foreach ($itemCollection as $item) {
                            if ($productId == $item['product_id'])
                                $idOnWishlist = $item['wishlist_item_id'];
                        }
                    }
                }
            }
            $observerData['data'][0]['wishlist_item_id'] = $idOnWishlist;
            $observerObject->setData($observerData);
        } catch (Exception $exc) {
            
        }
    }
	
	public function connectorCustomerSignInReturn($observer) {
		$observerObject = $observer->getObject();
		
        $observerData = $observer->getObject()->getData();
		if ($observerData['status'] == 'FAIL')
			return;
		
        $count = 0;
		$customer = Mage::getSingleton('customer/session')->getCustomer();

		if ($customer->getEntityId()!=null)
			$wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer, true);
		
        if ($wishlist)
            $collection = $wishlist->getItemCollection();
        if ($collection)
            $count = $collection->getSize();
		
        $observerData['data'][0]['wishlist_items_qty'] =  $count;
		$observerObject->setData($observerData);
	}

    function _getWishlistFromCustomer($customer = null) {
        if (!$customer)
            $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($customer->getId() && ($customer->getId() != '')) {
            $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer, true);
            return $wishlist;
        } else
            return null;
    }
    
    public function getConfig($value) {
        return Mage::getStoreConfig("appwishlist/general/" . $value);
    }
    

}
