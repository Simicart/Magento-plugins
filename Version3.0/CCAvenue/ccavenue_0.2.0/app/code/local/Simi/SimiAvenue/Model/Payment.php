<?php
/**
 *
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simiavenue
 * @copyright   Copyright (c) 2012 
 * @license    
 */

/**
 * Simiavenue Model
 * 
 * @category    
 * @package     Simiavenue
 * @author      Developer
 */
class Simi_SimiAvenue_Model_Payment extends Mage_Payment_Model_Method_Abstract {

	protected $_code = 'simiavenue';	
	protected $_infoBlockType = 'simiavenue/simiavenue';
	
	// public function getOrderPlaceRedirectUrl() {
		// return Mage::getUrl( 'simiavenue/api/redirect', array( '_secure' => true ) );
	// }
	
}