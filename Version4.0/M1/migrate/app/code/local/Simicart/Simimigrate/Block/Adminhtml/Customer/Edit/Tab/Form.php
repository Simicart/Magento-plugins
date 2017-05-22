<?php

/**

 */
class Simicart_Simimigrate_Block_Adminhtml_Customer_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        

        if (Mage::getSingleton('adminhtml/session')->getCustomerData()) {
            $data = Mage::getSingleton('adminhtml/session')->getCustomerData();
            Mage::getSingleton('adminhtml/session')->setCustomerData(null);
        } elseif (Mage::registry('customer_data'))
            $data = Mage::registry('customer_data')->getData();

        $fieldset = $form->addFieldset('customer_form', array('legend' => Mage::helper('simimigrate')->__('Customer information')));
        
        $fieldset->addField('customer_id', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Customer Id')
        ));
        
        $fieldset->addField('group_id', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Group Id')
        ));
        
        $fieldset->addField('email', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Customer Email')
        ));
        
        $form->setValues($data);
        return parent::_prepareForm();
    }

}
