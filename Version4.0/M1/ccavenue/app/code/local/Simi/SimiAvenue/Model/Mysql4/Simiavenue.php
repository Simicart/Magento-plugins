<?php
/**
 *
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simiavenue
 * @copyright   Copyright (c) 2012 
 * @license    
 */

/**
 * Simiavenue Resource Model
 * 
 * @category    
 * @package     Simiavenue
 * @author      Developer
 */
class Simi_SimiAvenue_Model_Mysql4_Simiavenue extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('simiavenue/simiavenue', 'simiavenue_id');
    }
}