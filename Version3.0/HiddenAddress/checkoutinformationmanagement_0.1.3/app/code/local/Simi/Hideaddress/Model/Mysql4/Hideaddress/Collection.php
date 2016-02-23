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
 * Hideaddress Resource Collection Model
 * 
 * @category    
 * @package     Hideaddress
 * @author      Developer
 */
class Simi_Hideaddress_Model_Mysql4_Hideaddress_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('hideaddress/hideaddress');
    }
}