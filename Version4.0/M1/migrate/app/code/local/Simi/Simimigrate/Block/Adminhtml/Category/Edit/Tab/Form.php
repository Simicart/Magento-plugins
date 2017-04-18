<?php

/**

 */
class Simi_Simimigrate_Block_Adminhtml_Category_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        

        if (Mage::getSingleton('adminhtml/session')->getCategoryData()) {
            $data = Mage::getSingleton('adminhtml/session')->getCategoryData();
            Mage::getSingleton('adminhtml/session')->setCategoryData(null);
        } elseif (Mage::registry('category_data'))
            $data = Mage::registry('category_data')->getData();

        $fieldset = $form->addFieldset('category_form', array('legend' => Mage::helper('simimigrate')->__('Category information')));
        
        $fieldset->addField('group_id', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Category (Group) Id')
        ));
        
        $fieldset->addField('name', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Category (Group) Name')
        ));
        
        $fieldset->addField('website_id', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Website Id')
        ));
        
        $fieldset->addField('root_category_id', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Root Category Id')
        ));
        
        $fieldset->addField('default_category_id', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Default Category View Id')
        ));
        
        $form->setValues($data);
        return parent::_prepareForm();
    }

}
