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
 * @package 	Magestore_Simiipay88
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Simiipay88 Status Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Simiipay88
 * @author  	Magestore Developer
 */
class Simi_Simiipay88_Model_Status extends Varien_Object
{
	const STATUS_ENABLED	= 1;
	const STATUS_DISABLED	= 2;
	
	/**
	 * get model option as array
	 *
	 * @return array
	 */
	static public function getOptionArray(){
		return array(
			self::STATUS_ENABLED	=> Mage::helper('simiipay88')->__('Enabled'),
			self::STATUS_DISABLED   => Mage::helper('simiipay88')->__('Disabled')
		);
	}
	
	/**
	 * get model option hash as array
	 *
	 * @return array
	 */
	static public function getOptionHash(){
		$options = array();
		foreach (self::getOptionArray() as $value => $label)
			$options[] = array(
				'value'	=> $value,
				'label'	=> $label
			);
		return $options;
	}
}