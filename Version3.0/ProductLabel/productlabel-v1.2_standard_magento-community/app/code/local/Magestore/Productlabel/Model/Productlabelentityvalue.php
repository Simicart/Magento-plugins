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
class Magestore_Productlabel_Model_Productlabelentityvalue extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('productlabel/productlabelentityvalue');
    }

    public function loadAttributeValue($labelentityId, $storeId, $attributeCode) {
        $attributeValue = $this->getCollection()
                ->addFieldToFilter('product_label_entity_id', $labelentityId)
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('attribute_code', $attributeCode)
                ->getFirstItem();

        $this->setData('product_label_entity_id', $labelentityId)
                ->setData('store_id', $storeId)
                ->setData('attribute_code', $attributeCode);
        if ($attributeValue) {

            $this->addData($attributeValue->getData())
                    ->setId($attributeValue->getId());
        }

        return $this;
    }

}