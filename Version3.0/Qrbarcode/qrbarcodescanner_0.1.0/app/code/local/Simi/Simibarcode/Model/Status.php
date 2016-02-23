<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Simibarcode
 * @copyright 	Copyright (c) 2012 
 * @license 	
 */

 /**
 * Simibarcode Model
 * 
 * @category 	
 * @package 	Simibarcode
 * @author  	Developer
 */
class Simi_Simibarcode_Model_Status extends Varien_Object
{
	const STATUS_ENABLED	= 1;
	const STATUS_DISABLED	= 0;
	
	/**
	 * get model option as array
	 *
	 * @return array
	 */
	public function getOptionArray()
	{
		return array(
			1	=> Mage::helper('simibarcode')->__('Enabled'),
			2   => Mage::helper('simibarcode')->__('Disabled')
		);
	}
	
	/**
	 * get model option hash as array
	 *
	 * @return array
	 */
	public function getOptionHash()
	{
		$options = array();
		foreach ($this->getOptionArray() as $value => $label)
			$options[] = array(
				'value'	=> $value,
				'label'	=> $label
			);
		return $options;
	}
}