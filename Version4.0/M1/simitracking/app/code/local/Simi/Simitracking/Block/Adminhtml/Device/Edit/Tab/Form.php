<?php

/**

 */
class Simi_Simitracking_Block_Adminhtml_Device_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        

        if (Mage::getSingleton('adminhtml/session')->getDeviceData()) {
            $data = Mage::getSingleton('adminhtml/session')->getDeviceData();
            Mage::getSingleton('adminhtml/session')->setDeviceData(null);
        } elseif (Mage::registry('device_data'))
            $data = Mage::registry('device_data')->getData();

        $fieldset = $form->addFieldset('device_form', array('legend' => Mage::helper('simitracking')->__('Device information')));
        $fieldset->addType('datetime', 'Simi_Simitracking_Block_Adminhtml_Device_Edit_Renderer_Datetime');
        $fieldset->addType('selectname', 'Simi_Simitracking_Block_Adminhtml_Device_Edit_Renderer_Selectname');

        $stores = Mage::getModel('core/store')->getCollection();

        $list_store = array();
        foreach ($stores as $store) {
            $list_store[] = array(
                'value' => $store->getId(),
                'label' => $store->getName(),
            );
        }
        $fieldset->addField('storeview_id', 'select', array(
            'label' => Mage::helper('simitracking')->__('Store View'),
            'name' => 'storeview_id',
            'values' => $list_store,
            'disabled' => true,
            'onchange' => 'clearDevices()'
        ));
        
        $fieldset->addField('plaform_id', 'select', array(
            'label' => Mage::helper('simitracking')->__('Device Type'),
            'name' => 'plaform_id',
            'values' => array(
                array('value' => 1, 'label' => Mage::helper('simitracking')->__('iPhone')),
                array('value' => 2, 'label' => Mage::helper('simitracking')->__('iPad')),
                array('value' => 3, 'label' => Mage::helper('simitracking')->__('Android')),
            ),
            'disabled' => true,
        ));

        $fieldset->addField('device_token', 'label', array(
            'label' => Mage::helper('simitracking')->__('Device Token'),
        ));
        
        $fieldset->addField('device_user_agent', 'label', array(
            'label' => Mage::helper('simitracking')->__('Device User Agent'),
        ));

        $fieldset->addField('created_time', 'datetime', array(
            'label' => Mage::helper('simitracking')->__('Updated Date'),
            'bold' => true,
            'name' => 'created_date',
        ));
        $form->setValues($data);
        return parent::_prepareForm();
    }

}
