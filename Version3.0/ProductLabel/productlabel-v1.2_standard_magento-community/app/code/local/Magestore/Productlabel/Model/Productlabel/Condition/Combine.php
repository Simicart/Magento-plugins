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
class Magestore_Productlabel_Model_Productlabel_Condition_Combine extends Mage_Rule_Model_Condition_Combine {

    public function __construct() {
        parent::__construct();
        $this->setType('productlabel/productlabel_condition_combine');
    }

    public function getNewChildSelectOptions() {
        if (version_compare(Mage::getVersion(), '1.7.0.0', '<'))
        {
            $version='Old';
        }
        else 
            $version='';
        $productCondition = Mage::getModel('productlabel/productlabel_condition_product'.$version);
        
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($productAttributes as $code => $label) {
            $attributes[] = array('value' => 'productlabel/productlabel_condition_product'.$version.'|' . $code, 'label' => $label);
        }
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value' => 'productlabel/productlabel_condition_combine', 'label' => Mage::helper('productlabel')->__('Conditions Combination')),
            array('label' => Mage::helper('productlabel')->__('Product Attribute'), 'value' => $attributes),
        ));
        $additional = new Varien_Object();
        if ($additionalConditions = $additional->getConditions()) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }
        return $conditions;
    }

    public function collectValidatedAttributes($productCollection) {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }

}
