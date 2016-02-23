<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Hideaddress
 * @copyright   Copyright (c) 2012 
 * @license   
 */

/**
 * Hideaddress Resource Model
 * 
 * @category    
 * @package     Hideaddress
 * @author      Developer
 */
class Simi_Hideaddress_Model_Mysql4_Hideaddress extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('hideaddress/hideaddress', 'hideaddress_id');
    }
}