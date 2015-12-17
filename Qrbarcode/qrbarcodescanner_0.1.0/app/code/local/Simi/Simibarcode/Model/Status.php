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
 * @package 	Magestore_Simibarcode
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Simibarcode Status Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Simibarcode
 * @author  	Magestore Developer
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