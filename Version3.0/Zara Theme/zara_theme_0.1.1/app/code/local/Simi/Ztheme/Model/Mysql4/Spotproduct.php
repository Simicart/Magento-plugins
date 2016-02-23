<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Ztheme
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Ztheme Resource Model
 * 
 * @category    
 * @package     Ztheme
 * @author      Developer
 */
class Simi_Ztheme_Model_Mysql4_Spotproduct extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('ztheme/spotproduct', 'spotproduct_id');
    }
}