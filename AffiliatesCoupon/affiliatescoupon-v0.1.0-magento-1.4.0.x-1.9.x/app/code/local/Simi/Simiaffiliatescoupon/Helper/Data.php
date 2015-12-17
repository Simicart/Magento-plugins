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
 * @category 	Magestore
 * @package 	Magestore_Simiaffiliatescoupon
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Simiaffiliatescoupon Helper
 * 
 * @category 	Magestore
 * @package 	Magestore_Simiaffiliatescoupon
 * @author  	Magestore Developer
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