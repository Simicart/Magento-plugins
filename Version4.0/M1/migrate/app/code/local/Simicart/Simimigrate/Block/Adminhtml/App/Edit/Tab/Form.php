<?php

/**

 */
class Simicart_Simimigrate_Block_Adminhtml_App_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        

        if (Mage::getSingleton('adminhtml/session')->getAppData()) {
            $data = Mage::getSingleton('adminhtml/session')->getAppData();
            Mage::getSingleton('adminhtml/session')->setAppData(null);
        } elseif (Mage::registry('app_data'))
            $data = Mage::registry('app_data')->getData();

        $fieldset = $form->addFieldset('app_form', array('legend' => Mage::helper('simimigrate')->__('App information')));
        
        $fieldset->addField('simicart_app_config_id', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Simicart App Config Id')
        ));
        
        $fieldset->addField('simicart_customer_id', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Simicart Customer Id')
        ));
        
        $fieldset->addField('website_url', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Website URL')
        ));
        
        $fieldset->addField('user_email', 'label', array(
            'label' => Mage::helper('simimigrate')->__('User Email')
        ));
        
        $fieldset->addField('user_name', 'label', array(
            'label' => Mage::helper('simimigrate')->__('User Name')
        ));
        
        $form->setValues($data);
        return parent::_prepareForm();
    }

}
