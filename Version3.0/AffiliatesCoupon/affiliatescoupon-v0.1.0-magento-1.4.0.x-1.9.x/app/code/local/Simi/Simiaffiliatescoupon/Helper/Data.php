<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simiaffiliatescoupon
 * @copyright   Copyright (c) 2012 
 * @license     
 */

 /**
 * Simiaffiliatescoupon Helper
 * 
 * @category    
 * @package     Simiaffiliatescoupon
 * @author      Developer
 */
class Simi_Simiaffiliatescoupon_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	public function getConfig($value) {
        return Mage::getStoreConfig("simiaffiliatescoupon/general/" . $value);
    }

    public function getAffiliatesDiscount() {    	
        $quote = Mage::getSingleton('checkout/cart')->getQuote();
        $quote->collectTotals()->save();
        $affiliatesDiscount = 0;
        foreach ($quote->getAddressesCollection() as $address){
            if ($address->getAffiliateplusDiscount()) {
                $affiliatesDiscount += abs($address->getAffiliateplusDiscount());
                break;
            }
        }
        return $affiliatesDiscount;
    }
}