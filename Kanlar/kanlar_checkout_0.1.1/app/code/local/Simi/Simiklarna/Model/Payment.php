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
 * @package     Magestore_SimiAvenue
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Simiavenue Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiAvenue
 * @author      Magestore Developer
 */
class Simi_Simiklarna_Model_Payment extends Mage_Payment_Model_Method_Abstract {

	protected $_code = 'simiklarna';	
	protected $_infoBlockType = 'simiklarna/simiklarna';
	
	// public function getOrderPlaceRedirectUrl() {
		// return Mage::getUrl( 'simiavenue/api/redirect', array( '_secure' => true ) );
	// }
	
}