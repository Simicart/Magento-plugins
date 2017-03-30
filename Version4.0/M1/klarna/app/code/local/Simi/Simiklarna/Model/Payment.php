<?php

class Simi_Simiklarna_Model_Payment extends Mage_Payment_Model_Method_Abstract {

	protected $_code = 'simiklarna';	
	protected $_infoBlockType = 'simiklarna/simiklarna';
	
	// public function getOrderPlaceRedirectUrl() {
		// return Mage::getUrl( 'simiavenue/api/redirect', array( '_secure' => true ) );
	// }
	
}