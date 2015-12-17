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
 * @package 	Magestore_Simicheckoutcom
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Simicheckoutcom Status Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Simicheckoutcom
 * @author  	Magestore Developer
 */
class Simi_Simicheckoutcom_Model_Status extends Varien_Object
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
			self::STATUS_ENABLED	=> Mage::helper('simicheckoutcom')->__('Enabled'),
			self::STATUS_DISABLED   => Mage::helper('simicheckoutcom')->__('Disabled')
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