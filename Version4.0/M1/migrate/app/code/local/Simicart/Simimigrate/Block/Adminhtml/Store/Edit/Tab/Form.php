<?php

/**

 */
class Simicart_Simimigrate_Block_Adminhtml_Store_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        

        if (Mage::getSingleton('adminhtml/session')->getStoreData()) {
            $data = Mage::getSingleton('adminhtml/session')->getStoreData();
            Mage::getSingleton('adminhtml/session')->setStoreData(null);
        } elseif (Mage::registry('store_data'))
            $data = Mage::registry('store_data')->getData();

        $fieldset = $form->addFieldset('store_form', array('legend' => Mage::helper('simimigrate')->__('Store information')));
        
        $fieldset->addField('group_id', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Store (Group) Id')
        ));
        
        $fieldset->addField('name', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Store (Group) Name')
        ));
        
        $fieldset->addField('website_id', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Website Id')
        ));
        
        $fieldset->addField('root_category_id', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Root Category Id')
        ));
        
        $fieldset->addField('default_store_id', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Default Store View Id')
        ));
        
        $form->setValues($data);
        return parent::_prepareForm();
    }

}
