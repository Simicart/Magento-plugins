<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.simicart.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Magestore
 * @package 	Magestore_Paypalmobile
 * @copyright 	Copyright (c) 2012 Magestore (http://www.simicart.com/)
 * @license 	http://www.simicart.com/license-agreement.html
 */

 /**
 * Paypalmobile Status Model
 * 
 * @category 	Simi
 * @package 	Simi_Simibraintree
 * @author  	Simi Developer
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