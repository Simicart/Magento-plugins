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
 * Simiavenue Resource Collection Model
 * 
 * @category    
 * @package     Simiavenue
 * @author      Developer
 */
class Simi_SimiAvenue_Model_Mysql4_Simiavenue_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('simiavenue/simiavenue');
    }
}