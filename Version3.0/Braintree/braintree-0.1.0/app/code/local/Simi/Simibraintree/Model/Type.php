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
class Simi_Simibraintree_Model_Type extends Varien_Object
{
	const TYPE_SALE	 = 1;
	const TYPE_AUTHORISE = 0;
	
	/**
	 * get model option as array
	 *
	 * @return array
	 */
	static public function getOptionArray(){
		return array(
			self::TYPE_SALE	=> Mage::helper('simibraintree')->__('Sale'),
			self::TYPE_AUTHORISE   => Mage::helper('simibraintree')->__('Authorise')
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
}