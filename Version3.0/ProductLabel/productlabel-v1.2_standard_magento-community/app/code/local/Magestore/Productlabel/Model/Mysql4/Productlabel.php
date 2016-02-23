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
class Magestore_Productlabel_Model_Mysql4_Productlabel extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('productlabel/productlabel', 'label_id');
    }

    public function updateProductLabelData(Magestore_Productlabel_Model_Productlabel $rule) {
        $ruleId = $rule->getId();
        $write = $this->_getWriteAdapter();
        $write->beginTransaction();
        $productsFilter = $rule->getProductsFilter();
        $store_id = $rule->getStoreId();
        if ($productsFilter) {
            $write->delete(
                    $this->getTable('productlabel/productlabelflatdata'), array(
                'label_id=?' => $ruleId,
                'store_id=?' => $store_id,
                'product_id IN (?)' => $productsFilter
                    )
            );
        } else {
            $write->delete(
                    $this->getTable('productlabel/productlabelflatdata'), array(
                'label_id=?' => $ruleId,
                'store_id=?' => $store_id,
                    )
            );
        }

        if ($rule->getStatus() == 2) {


            $write->commit();
            return $this;
        }

        Varien_Profiler::start('__MATCH_PRODUCTS__');
        $productIds = $rule->getMatchingProductIds();
        Varien_Profiler::stop('__MATCH_PRODUCTS__');

        $text = $rule->getText();
        $image = $rule->getImage();
        $position = $rule->getPosition();
        $display = $rule->getDisplay();
        $category_text = $rule->getCategoryText();
        $category_image = $rule->getCategoryImage();
        $category_position = $rule->getCategoryPosition();
        $category_display = $rule->getCategoryDisplay();
        $from_time = $rule->getFromtime();
        $to_time = $rule->getTotime();
        $priority = (int) $rule->getPriority();

        $rows = array();

        try {
            foreach ($productIds as $productId) {
                $fromTime = strtotime($from_time[$productId]);
                $toTime = strtotime($to_time[$productId]);
                $rows[] = array(
                    'text' => Mage::helper('productlabel')->convertBackendString($text, Mage::getModel('catalog/product')->load($productId), $store_id),
                    'image' => $image,
                    'position' => $position,
                    'display' => $display,
                    'category_text' => Mage::helper('productlabel')->convertBackendString($category_text, Mage::getModel('catalog/product')->load($productId), $store_id),
                    'category_image' => $category_image,
                    'category_position' => $category_position,
                    'category_display' => $category_display,
                    'label_id' => $ruleId,
                    'from_time' => $fromTime,
                    'to_time' => $toTime,
                    'product_id' => $productId,
                    'priority' => $priority,
                    'store_id' => $store_id
                );
                if (count($rows) == 1000) {
                    $write->insertMultiple($this->getTable('productlabel/productlabelflatdata'), $rows);
                    $rows = array();
                }
            }
            if (!empty($rows)) {
                $write->insertMultiple($this->getTable('productlabel/productlabelflatdata'), $rows);
            }
            $write->commit();
        } catch (Exception $e) {
            $write->rollback();
            throw $e;
        }



        return $this;
    }

}