<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simipayfort
 * @copyright   Copyright (c) 2017 
 * @license     
 */

 /**
 * Simipayfort Block
 * 
 * @category    
 * @package     Simipayfort
 * @author      Developer
 */
class Simi_Simipayfort_Block_Payment extends Mage_Payment_Block_Info_Cc
{
	protected $_tranS;
    
    public function getCcTypeName() {
        return '';
    }
}