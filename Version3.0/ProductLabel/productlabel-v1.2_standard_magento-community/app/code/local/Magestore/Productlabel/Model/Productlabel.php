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
class Magestore_Productlabel_Model_Productlabel extends Mage_Rule_Model_Rule {

    protected $_productIds;
    protected $_storeId = null;

    /**
     * Limitation for products collection
     *
     * @var int|array|null
     */
    protected $_productsFilter = null;
    //    from time aplly label
    protected $_fromtime;
    //    end time aplly label
    protected $_totime;

    public function getStoreId() {
        return $this->_storeId;
    }

    public function setStoreId($storeId) {
        $this->_storeId = $storeId;
        return $this;
    }

    public function getStoreAttributes() {
        return array(
            'status',
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
        $storeValues = Mage::getModel('productlabel/productlabelvalue')->getCollection()
                ->addFieldToFilter('label_id', $this->getId())
                ->addFieldToFilter('store_id', $storeId);
        foreach ($storeValues as $value) {
            $this->setData($value->getAttributeCode() . '_in_store', true);
            $this->setData($value->getAttributeCode(), $value->getValue());
        }

        return $this;
    }

    protected function _beforeSave() {
        if ($storeId = $this->getStoreId()) {
            $defaultLabel = Mage::getModel('productlabel/productlabel')->load($this->getId());
            $storeAttributes = $this->getStoreAttributes();
            foreach ($storeAttributes as $attribute) {
                if ($this->getData($attribute . '_default')) {
                    $this->setData($attribute . '_in_store', false);
                } else {
                    $this->setData($attribute . '_in_store', true);
                    $this->setData($attribute . '_value', $this->getData($attribute));
                }
                $this->setData($attribute, $defaultLabel->getData($attribute));
            }
            if ($this->getData('is_auto_fill')) {
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
                $attributeValue = Mage::getModel('productlabel/productlabelvalue')
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
        $this->_init('productlabel/productlabel');
        $this->setIdFieldName('label_id');
    }

    public function getConditionsInstance() {
        return Mage::getModel('productlabel/productlabel_condition_combine');
    }

    /**
     * Get array of product ids which are matched by rule
     *
     * @return array
     */
    public function getMatchingProductIds() {
        if (is_null($this->_productIds)) {
            $this->_productIds = array();
            $this->setCollectedAttributes(array());
//            when admin choiced custom condition
            if ($this->getConditionSelected() == 'custom') {
                /** @var $productCollection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
                $productCollection = Mage::getResourceModel('catalog/product_collection');
                if ($this->_productsFilter) {
                    $productCollection->addIdFilter($this->_productsFilter);
                }
                $this->getConditions()->collectValidatedAttributes($productCollection);

                Mage::getSingleton('core/resource_iterator')->walk(
                        $productCollection->getSelect(), array(array($this, 'callbackValidateProduct')), array(
                    'attributes' => $this->getCollectedAttributes(),
                    'product' => Mage::getModel('catalog/product'),
                        )
                );
            }
//            when admin choice onsale condition
            if ($this->getConditionSelected() == 'onsale') {

                $productCollection = Mage::getModel('catalog/product')
                        ->getCollection()->addStoreFilter($this->getStoreid())
                        ->addAttributeToSelect(array(
                            'special_from_date',
                            'special_to_date',
                            'special_price','price'));
                if ($this->_productsFilter) {
                    $productCollection->addIdFilter($this->_productsFilter);
                }
                foreach ($productCollection as $product) {
                    $this->validateOnsaleProduct($product);
                }
            }

//            when admin choiced newproduct
            if ($this->getConditionSelected() == 'newproduct') {

                $productCollection = Mage::getModel('catalog/product')
                        ->getCollection()->addStoreFilter($this->getStoreid())
                        ->addAttributeToSelect(array(
                            'news_from_date',
                            'news_to_date'
                            ));
                if ($this->_productsFilter) {
                    $productCollection->addIdFilter($this->_productsFilter);
                }
                foreach ($productCollection as $product) {
                    $this->validateNewProduct($product);
                }
            }
        }
        return $this->_productIds;
    }

    public function validateNewProduct($product) {
        $newFromDate = $product->getNewsFromDate();
        $newToDate = $product->getNewsToDate();
        if ($newFromDate || $newToDate) {

            $fromdate = $this->getFromDate();
            $todate = $this->getToDate();
            if (strtotime($newFromDate) < strtotime($fromdate)) {
                $newFromDate = $fromdate;
            }
            if ($todate) {
                if ($newToDate) {
                    if (strtotime($newToDate) > strtotime($todate)) {
                        $newToDate = $todate;
                    }
                }
                else
                    $newToDate = $todate;
            }
            if ($newToDate) {
                $now = date('Y-m-d H:m:s');
                if (strtotime($newFromDate) < strtotime($now) && strtotime($newToDate) > strtotime($now)) {

                    $this->_productIds[] = $product->getId();
                    $this->_fromtime[$product->getId()] = $newFromDate;
                    $this->_totime[$product->getId()] = $newToDate;
                    return true;
                }
            } else {
                if ($newFromDate) {
                    $this->_productIds[] = $product->getId();
                    $this->_fromtime[$product->getId()] = $newFromDate;
                    $this->_totime[$product->getId()] = $newToDate;
                    return true;
                }
            }
        }
        return false;
    }

    public function validateOnsaleProduct($product) {
		$pId        = $product->getId();
        $storeId    = $product->getStoreId();
		$date = Mage::app()->getLocale()->storeTimeStamp($storeId);
		if ($product->hasCustomerGroupId()) {
            $gId = $product->getCustomerGroupId();
        } else {
            $gId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }
		
		$websites = Mage::app()->getWebsites();
		
		$final_price = 0;
		
		foreach($websites as $website) {
			$wId = $website->getId();
			if($wId) {
				
				$key = "$date|$wId|$gId|$pId";
				
				
				
				$rulePrice = Mage::getResourceModel('catalogrule/rule')
					->getRulePrice($date, $wId, $gId, $pId);
				
				$final_price = $rulePrice;
				
				break;
				
			} 
			
		}
		$special_price = $product->getFinalPrice();
		if($special_price && $final_price) {
			$special_price = min($final_price, $special_price);
		} elseif($final_price && $special_price == 0) {
			$special_price = $final_price;
		} else {
			$special_price = $special_price;
		} 

        if ($special_price) {
            if ($special_price < $price = $product->getPrice()) {
				
                $discount = 100 - $special_price / $price * 100;
                if ($this->getThreshold() < $discount) {
                    $fromdate = $this->getFromDate();
                    $todate = $this->getToDate();
                    $specialFromDate = $product->getSpecialFromDate();
                    $specialToDate = $product->getSpecialToDate();

                    if (strtotime($specialFromDate) < strtotime($fromdate)) {
                        $specialFromDate = $fromdate;
                    }
                    if ($todate = $this->getToDate()) {
                        if ($specialToDate) {
                            if (strtotime($specialToDate) > strtotime($todate)) {
                                $specialToDate = $todate;
                            }
                        }
                        else
                            $specialToDate = $todate;
                    }

                    $this->_productIds[] = $product->getId();
                    $this->_fromtime[$product->getId()] = $specialFromDate;
                    $this->_totime[$product->getId()] = $specialToDate;
					
                    return true;
                }
            }
        } 
		
        return false;
    }

    public function callbackValidateProduct($args) {
        $product = clone $args['product'];
        $product->setData($args['row']);
        if ($this->getConditions()->validate($product)) {
            $this->_productIds[] = $product->getId();
            $this->_fromtime[$product->getId()] = $this->getFromDate();
            $this->_totime[$product->getId()] = $this->getToDate();
        }
    }

    public function applyAll() {

        foreach (Mage::app()->getStores() as $store) {
            $collection = $this->getResourceCollection()->setStoreId($store->getId());
            if (version_compare(Mage::getVersion(), '1.7.0.0', '<')) {
                foreach ($collection as $rule) {
                    $rule->afterLoad();
                   
                }
            }

            $collection->walk(array($this->_getResource(), 'updateProductLabelData'));
        }
    }

    /**
     * Filtering products that must be checked for matching with rule
     *
     * @param  int|array $productIds
     */
    public function setProductsFilter($productIds) {
        $this->_productsFilter = $productIds;
    }

    /**
     * Returns products filter
     *
     * @return array|int|null
     */
    public function getProductsFilter() {
        return $this->_productsFilter;
    }

    public function getFromtime() {
        return $this->_fromtime;
    }

    public function getTotime() {
        return $this->_totime;
    }

}