<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Productlabel
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Productlabel Model
 * 
 * @category    Magestore
 * @package     Magestore_Productlabel
 * @author      Magestore Developer
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