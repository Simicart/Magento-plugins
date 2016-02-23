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
 * Simiavenue Model
 * 
 * @category    
 * @package     Simiavenue
 * @author      Developer
 */
class Simi_SimiAvenue_Model_Simiavenue extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('simiavenue/simiavenue');
    }
}