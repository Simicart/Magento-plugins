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
 * @package     Magestore_Loyalty
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Loyalty Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Loyalty
 * @author      Magestore Developer
 */
class Magestore_Loyalty_Model_Observer
{
    public function salesQuoteCollectTotalsAfter($observer)
    {
    	if (Mage::app()->getRequest()->getControllerModule() == 'Simi_Connector' || Mage::app()->getStore()->isAdmin()) {
            return Mage::getSingleton('rewardpoints/total_quote_earning')
                ->salesQuoteCollectTotalsAfter($observer);
    	}
    }
    
    /**
     * Attach Reward Information when load product detail
     */
    public function productDetailRewardPoints($observer)
    {
    	if (!Mage::helper('loyalty')->isShowOnProduct()) {
    		return ;
    	}
    	$model     = $observer['object'];
    	$product   = $observer['product'];
    	
    	$block = Mage::getBlockSingleton('rewardpoints/product_view_earning');
    	if (!Mage::registry('product')) {
    		Mage::register('product', $product);
    	}
    	if ($block->hasEarningRate()) {
            $_product  = $model->getData();
            
            $_product['loyalty_image'] = Mage::helper('rewardpoints/point')->getImage();
            $_product['loyalty_label'] = $block->__('You could receive some %s for purchasing this product', $block->getPluralPointName());
            
            $model->setData($_product);
    	}
    }
    
    /**
     * Get Reward Points Configuration for Customer Checkout
     */
    public function checkoutOrderConfigRewardPoints($observer)
    {
    	$helper = Mage::helper('loyalty/block_spend');
    	if (!$helper->enableReward()) {
    		return ;
    	}
    	$model = $observer['object'];
    	$pointSpending = 0;
    	$pointDiscount = 0.00;
    	$pointEarning  = 0;
    	foreach ($helper->getQuote()->getAllAddresses() as $address) {
    		if ($address->getRewardpointsSpent()) {
    			$pointSpending = (int)$address->getRewardpointsSpent();
    			$pointDiscount = $address->getRewardpointsDiscount();
    		}
    		if ($address->getRewardpointsEarn()) {
    			$pointEarning = (int)$address->getRewardpointsEarn();
    		}
    	}
    	$model->setLoyaltySpend($pointSpending);
    	$model->setLoyaltyDiscount($pointDiscount);
    	$model->setLoyaltyEarn($pointEarning);
    	$model->setLoyaltySpending(Mage::helper('rewardpoints/point')->format($pointSpending));
    	$model->setLoyaltyEarning(Mage::helper('rewardpoints/point')->format($pointEarning));
    	$model->setLoyaltyRules($helper->getSliderRulesFormatted());
    }
    
    /**
     * Update menu after checkout
     */
    public function checkoutOrderPlaceAfter($observer)
    {
    	if (Mage::getSingleton('customer/session')->isLoggedIn()) {
    	   $model = $observer['object'];
    	   $model->setLoyaltyBalance(Mage::helper('loyalty')->getMenuBalance());
    	}
    }
}
