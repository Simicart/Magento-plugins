<?php

/**

 */
class Simicart_Simimigrate_Block_Adminhtml_Product_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        

        if (Mage::getSingleton('adminhtml/session')->getProductData()) {
            $data = Mage::getSingleton('adminhtml/session')->getProductData();
            Mage::getSingleton('adminhtml/session')->setProductData(null);
        } elseif (Mage::registry('product_data'))
            $data = Mage::registry('product_data')->getData();

        $fieldset = $form->addFieldset('product_form', array('legend' => Mage::helper('simimigrate')->__('Product information')));
        
        $fieldset->addField('product_id', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Product Id')
        ));
        
        $fieldset->addField('sku', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Product Sku')
        ));
        
        $fieldset->addField('name', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Product Name')
        ));
        
        $fieldset->addField('has_options', 'select', array(
            'label' => Mage::helper('simimigrate')->__('Has Option'),
            'name' => 'has_options',
            'values' => array(
                array('value' => 0, 'label' => Mage::helper('simimigrate')->__('No')),
                array('value' => 1, 'label' => Mage::helper('simimigrate')->__('Yes'))
            ),
            'disabled' => true,
        ));
        
        $fieldset->addField('required_options', 'select', array(
            'label' => Mage::helper('simimigrate')->__('Required Option'),
            'name' => 'required_options',
            'values' => array(
                array('value' => 0, 'label' => Mage::helper('simimigrate')->__('No')),
                array('value' => 1, 'label' => Mage::helper('simimigrate')->__('Yes'))
            ),
            'disabled' => true,
        ));
        
        $fieldset->addField('is_salable', 'select', array(
            'label' => Mage::helper('simimigrate')->__('Is Salable'),
            'name' => 'is_salable',
            'values' => array(
                array('value' => 0, 'label' => Mage::helper('simimigrate')->__('No')),
                array('value' => 1, 'label' => Mage::helper('simimigrate')->__('Yes'))
            ),
            'disabled' => true,
        ));
        
        $form->setValues($data);
        return parent::_prepareForm();
    }

}
