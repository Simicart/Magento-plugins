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
class Magestore_Productlabel_Model_Productlabelvalue extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('productlabel/productlabelvalue');
    }

    public function loadAttributeValue($labelId, $storeId, $attributeCode) {
        $attributeValue = $this->getCollection()
                ->addFieldToFilter('label_id', $labelId)
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('attribute_code', $attributeCode)
                ->getFirstItem();
        $this->setData('label_id', $labelId)
                ->setData('store_id', $storeId)
                ->setData('attribute_code', $attributeCode);
        if ($attributeValue)
            $this->addData($attributeValue->getData())
                    ->setId($attributeValue->getId());
        return $this;
    }

}