<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Loyalty
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Loyalty Helper
 * 
 * @category    
 * @package     Loyalty
 * @author      Developer
 */
class Magestore_Loyalty_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_SHOW_PRODUCT    = 'rewardpoints/loyalty/product';
	const XML_PATH_SHOW_CART       = 'rewardpoints/loyalty/cart';
	
	/*
    public function getImage($store = null)
    {
    	if ($imgPath = Mage::getStoreConfig(Magestore_RewardPoints_Helper_Point::XML_PATH_POINT_IMAGE, $store)) {
            return Mage::getBaseUrl('media') . 'rewardpoints/' . $imgPath;
        }
        return Mage::getDesign()->getSkinUrl('images/rewardpoints/point.png');
    }
    */
	
	public function isShowOnProduct($store = null)
	{
		return Mage::getStoreConfigFlag(self::XML_PATH_SHOW_PRODUCT, $store);
	}
	
	public function getMenuBalance()
	{
		$helper = Mage::helper('rewardpoints/customer');
		$pointAmount = $helper->getBalance();
		if ($pointAmount > 0) {
			$rate = Mage::getModel('rewardpoints/rate')->getRate(Magestore_RewardPoints_Model_Rate::POINT_TO_MONEY);
			if ($rate && $rate->getId()) {
				$baseAmount = $pointAmount * $rate->getMoney() / $rate->getPoints();
				return Mage::app()->getStore()->convertPrice($baseAmount, true, false);
			}
		}
		return $helper->getBalanceFormated();
	}
	
	public function cardConfig($field, $store = null)
	{
		return Mage::getStoreConfig('rewardpoints/passbook/' . $field, $store);
	}
}
