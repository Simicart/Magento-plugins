<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Simiaffiliatescoupon
 * @copyright 	Copyright (c) 2012 
 * @license 	
 */

 /**
 * Simiaffiliatescoupon Index Controller
 * 
 * @category 	
 * @package 	Simiaffiliatescoupon
 * @author      Developer
 */
class Simi_Simiaffiliatescoupon_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
     * checkInstall action
     */
    public function checkInstallAction()
    {		
        echo "1";
		exit();
    }

    /**
     * checkInstall action
     */
    public function test2Action()
    {		
        Mage::helper('simiaffiliatescoupon')->getAffiliatesDiscount();
    }
}