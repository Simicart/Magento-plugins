<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Productlabel
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Productlabel Model
 * 
 * @category    
 * @package     Productlabel
 * @author      Developer
 */
class Magestore_Productlabel_Model_Mysql4_Productlabelentity extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('productlabel/productlabelentity', 'product_label_entity_id');
    }
    
}