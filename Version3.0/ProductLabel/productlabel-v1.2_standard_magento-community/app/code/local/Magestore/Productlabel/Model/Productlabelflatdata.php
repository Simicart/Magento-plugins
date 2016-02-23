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
class Magestore_Productlabel_Model_Productlabelflatdata extends Mage_Core_Model_Abstract {

    
    /**
     * Initialize resource
     */
    protected function _construct() {
        $this->_init('productlabel/productlabelflatdata');
    }

    protected function _beforeSave() {
        parent::_beforeSave();
    }

    protected function _afterSave() {
        parent::_afterSave();
    }

    /**
     * @param int $category_id
     * @param int $optionId
     * @param string $filter
     * @return int
     */
    

}