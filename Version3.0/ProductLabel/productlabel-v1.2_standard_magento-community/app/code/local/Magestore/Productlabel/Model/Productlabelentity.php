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
class Magestore_Productlabel_Model_Productlabelentity extends Mage_Core_Model_Abstract {

    protected $_storeId = null;

    public function getStoreId() {
        return $this->_storeId;
    }

    public function setStoreId($storeId) {
        $this->_storeId = $storeId;
        return $this;
    }

    public function getStoreAttributes() {
        return array(
            'display',
            'position',
            'text',
            'category_display',
            'category_position',
            'category_text'
        );
    }

    public function load($id, $field = null) {
        parent::load($id, $field);
        if ($this->getStoreId()) {
            $this->loadStoreValue();
        }
        return $this;
    }

    public function loadStoreValue($storeId = null) {
        if (!$storeId)
            $storeId = $this->getStoreId();
        if (!$storeId)
            return $this;
        $storeValues = Mage::getModel('productlabel/productlabelentityvalue')->getCollection()
                ->addFieldToFilter('product_label_entity_id', $this->getId())
                ->addFieldToFilter('store_id', $storeId);
        foreach ($storeValues as $value) {
            $this->setData($value->getAttributeCode() . '_in_store', true);
            $this->setData($value->getAttributeCode(), $value->getValue());
        }

        return $this;
    }

    protected function _beforeSave() {
        $storeAttributes = $this->getStoreAttributes();
        if ($storeId = $this->getStoreId()) {
            $defaultLabel = Mage::getModel('productlabel/productlabelentity')->load($this->getId());

            foreach ($storeAttributes as $attribute) {
                if ($this->getData($attribute . '_default')) {
                    $this->setData($attribute . '_in_store', false);
                } else {
                    $this->setData($attribute . '_in_store', true);
                    $this->setData($attribute . '_value', $this->getData($attribute));
                }
                $this->setData($attribute, $defaultLabel->getData($attribute));
            }
            if ($this->getData('same_on_two_page')) {
                if (!$this->getData('display_default')) {
                    $this->setData('category_display_in_store', true);
                    $this->setData('category_display_value', $this->getData('display_value'));
                }
                if (!$this->getData('position_default')) {
                    $this->setData('category_position_in_store', true);
                    $this->setData('category_position_value', $this->getData('position_value'));
                }
                if (!$this->getData('text_default')) {
                    $this->setData('category_text_in_store', true);
                    $this->setData('category_text_value', $this->getData('text_value'));
                }
            }
        }

        return parent::_beforeSave();
    }

    protected function _afterSave() {

        if ($storeId = $this->getStoreId()) {
            $storeAttributes = $this->getStoreAttributes();
            foreach ($storeAttributes as $attribute) {

                $attributeValue = Mage::getModel('productlabel/productlabelentityvalue')
                        ->loadAttributeValue($this->getId(), $storeId, $attribute);
                if ($this->getData($attribute . '_in_store')) {
                    try {

                        $attributeValue->setValue($this->getData($attribute . '_value'))
                                ->save();
                    } catch (Exception $e) {
                        
                    }
                } elseif ($attributeValue && $attributeValue->getId()) {
                    try {
                        $attributeValue->delete();
                    } catch (Exception $e) {
                        
                    }
                }
            }
        }
        return parent::_afterSave();
    }

    public function _construct() {
        parent::_construct();
        if ($storeId = Mage::app()->getStore()->getId()) {
            $this->setStoreId($storeId);
        }
        $this->_init('productlabel/productlabelentity');
    }

}