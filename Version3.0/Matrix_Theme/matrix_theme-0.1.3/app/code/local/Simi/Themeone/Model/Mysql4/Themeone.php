<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Themeone
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Themeone Model
 * 
 * @category    
 * @package     Themeone
 * @author      Developer
 */
class Simi_Themeone_Model_Mysql4_Themeone extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('themeone/themeone', 'themeone_id');
    }
}