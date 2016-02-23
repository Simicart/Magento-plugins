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
 * Loyalty Model
 * 
 * @category    
 * @package     Loyalty
 * @author      Developer
 */
class Magestore_Loyalty_Model_Total_Quote_Label extends Magestore_RewardPoints_Model_Total_Quote_Label
{
	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		if (Mage::app()->getStore()->isAdmin()) {
			return parent::fetch($address);
		}
		return $this;
	}
}
