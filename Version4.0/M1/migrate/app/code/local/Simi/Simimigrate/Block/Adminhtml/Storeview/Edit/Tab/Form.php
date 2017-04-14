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
        $fieldset->addType('datetime', 'Simi_Simimigrate_Block_Adminhtml_Storeview_Edit_Renderer_Datetime');
        $fieldset->addType('selectname', 'Simi_Simimigrate_Block_Adminhtml_Storeview_Edit_Renderer_Selectname');

        $stores = Mage::getModel('core/store')->getCollection();

        $list_store = array();
        foreach ($stores as $store) {
            $list_store[] = array(
                'value' => $store->getId(),
                'label' => $store->getName(),
            );
        }
        $fieldset->addField('storeview_id', 'select', array(
            'label' => Mage::helper('simimigrate')->__('Store View'),
            'name' => 'storeview_id',
            'values' => $list_store,
            'disabled' => true,
            'onchange' => 'clearStoreviews()'
        ));
        
        $fieldset->addField('plaform_id', 'select', array(
            'label' => Mage::helper('simimigrate')->__('Storeview Type'),
            'name' => 'plaform_id',
            'values' => array(
                array('value' => 1, 'label' => Mage::helper('simimigrate')->__('iPhone')),
                array('value' => 2, 'label' => Mage::helper('simimigrate')->__('iPad')),
                array('value' => 3, 'label' => Mage::helper('simimigrate')->__('Android')),
            ),
            'disabled' => true,
        ));

        $fieldset->addField('country', 'selectname', array(
            'label' => Mage::helper('simimigrate')->__('Country'),
            'bold' => true,
            'name' => 'country',
        ));

        $fieldset->addField('state', 'label', array(
            'label' => Mage::helper('simimigrate')->__('State/Province'),
                // 'bold'  => true,
        ));

        $fieldset->addField('city', 'label', array(
            'label' => Mage::helper('simimigrate')->__('City'),
                // 'bold'  => true,
        ));

        $fieldset->addField('storeview_token', 'label', array(
            'label' => Mage::helper('simimigrate')->__('Storeview Token'),
        ));

        $fieldset->addField('created_time', 'datetime', array(
            'label' => Mage::helper('simimigrate')->__('Create Date'),
            'bold' => true,
            'name' => 'created_date',
        ));

        $fieldset->addField('is_demo', 'select', array(
            'label' => Mage::helper('simimigrate')->__('Is Demo'),
            'bold' => true,
            'values' => array(
                array('value' => 3, 'label' => Mage::helper('simimigrate')->__('N/A')),
                array('value' => 0, 'label' => Mage::helper('simimigrate')->__('NO')),
                array('value' => 1, 'label' => Mage::helper('simimigrate')->__('YES')),
            ),
            'name' => 'is_demo',
            'disabled' => true,
        ));
        $form->setValues($data);
        return parent::_prepareForm();
    }

}
