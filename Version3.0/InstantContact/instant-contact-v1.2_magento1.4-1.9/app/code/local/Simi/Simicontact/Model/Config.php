<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Simicontact
 * @copyright 	Copyright (c) 2012 
 * @license 	
 */

 /**
 * Simicontact Model
 * 
 * @category 	
 * @package 	Simicontact
 * @author  	Developer
 */
class Simi_Simicontact_Model_Config
{
	public function toOptionArray() {
        return array(
            array('value' => '1', 'label' => Mage::helper('core')->__('List')),
            array('value' => '2', 'label' => Mage::helper('core')->__('Grid'))
			);            
    }
}