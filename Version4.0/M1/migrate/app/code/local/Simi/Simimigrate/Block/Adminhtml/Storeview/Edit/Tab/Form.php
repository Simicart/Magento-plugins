<?php

/**

 */
class Simi_Simimigrate_Block_Adminhtml_Storeview_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        

        if (Mage::getSingleton('adminhtml/session')->getStoreviewData()) {
            $data = Mage::getSingleton('adminhtml/session')->getStoreviewData();
            Mage::getSingleton('adminhtml/session')->setStoreviewData(null);
        } elseif (Mage::registry('storeview_data'))
            $data = Mage::registry('storeview_data')->getData();

        $fieldset = $form->addFieldset('storeview_form', array('legend' => Mage::helper('simimigrate')->__('Storeview information')));
        
        $fieldset->addField('is_active', 'select', array(
            'label' => Mage::helper('simimigrate')->__('Is Actived'),
            'name' => 'is_active',
            'values' => array(
                array('value' => 0, 'label' => Mage::helper('simimigrate')->__('No')),
                array('value' => 1, 'label' => Mage::helper('simimigrate')->__('Yes'))
            ),
            'disabled' => true,
        ));

        $fieldset->addField('code', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Store View Code')
        ));
        
        $fieldset->addField('name', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Store View Name')
        ));
        
        $form->setValues($data);
        return parent::_prepareForm();
    }

}
