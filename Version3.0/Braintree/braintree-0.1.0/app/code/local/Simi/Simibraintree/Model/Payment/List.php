<?php
/**
 *
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simibraintree
 * @copyright   Copyright (c) 2012 
 * @license    
 */

/**
 * Simibraintree Model
 * 
 * @category    
 * @package     Simibraintree
 * @author      Developer
 */
class Simi_Simibraintree_Model_Payment_List extends Varien_Object
{
	const BRAINTREE_APPLE_PAY	= 'braintree_applepay';
	const BRAINTREE_CREDITCARD	= 'braintree_creditcard';
	const BRAINTREE_PAYPAL	    = 'braintree_paypal';
	const BRAINTREE_GOOGLEPAY	= 'braintree_googlepay';
	
	/**
	 * get model option as array
	 *
	 * @return array
	 */
	static public function getOptionArray(){
		return array(			
			// self::BRAINTREE_CREDITCARD   => Mage::helper('simibraintree')->__('Creditcard'),
			// self::BRAINTREE_GOOGLEPAY   => Mage::helper('simibraintree')->__('Google Pay'),
			self::BRAINTREE_PAYPAL   => Mage::helper('simibraintree')->__('Paypal'),
			self::BRAINTREE_APPLE_PAY	=> Mage::helper('simibraintree')->__('Apple Pay')			
		);
	}
	
	/**
	 * get model option hash as array
	 *
	 * @return array
	 */
	static public function toOptionArray(){
		$options = array();
		foreach (self::getOptionArray() as $value => $label)
			$options[] = array(
				'value'	=> $value,
				'label'	=> $label
			);
		return $options;
	}

	public function getPaymentTitle($code){
		$payment = $this->getOptionArray();
		$code = isset($payment[$code]) ? $payment[$code] : '';
		return $code;
	}
}