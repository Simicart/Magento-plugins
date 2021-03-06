<?php

class Simi_Simistorelocator_Block_Adminhtml_Holiday_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('holiday_form', array('legend' => Mage::helper('simistorelocator')->__('Holiday Information')));
        $image_calendar = Mage::getBaseUrl('skin') . 'adminhtml/default/default/images/grid-cal.gif';
        $fieldset->addField('store_id', 'multiselect', array(
            'label' => Mage::helper('simistorelocator')->__('Store'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'store_id',
            'values' => Mage::helper('simistorelocator')->getStoreOptions(),
            'note'  => Mage::helper('simistorelocator')->__('Select stores applied holiday(s).'),
        ));

        $fieldset->addField('date', 'date', array(
            'label' => Mage::helper('simistorelocator')->__('From Date'),
            'required' => true,
            'format' => 'yyyy-MM-dd',
            'image' => $image_calendar,
            'name' => 'date',
        ));

        $fieldset->addField('holiday_date_to', 'date', array(
            'label' => Mage::helper('simistorelocator')->__('To Date'),
            'required' => true,
            'format' => 'yyyy-MM-dd',
            'image' => $image_calendar,
            'name' => 'holiday_date_to',
        ));

        $fieldset->addField('comment', 'textarea', array(
            'name' => 'comment',
            'label' => Mage::helper('simistorelocator')->__('Comment'),
            'title' => Mage::helper('simistorelocator')->__('Comment'),
            'note' => Mage::helper('simistorelocator')->__('Notification message shown to customers'),
            //'style' => 'width:500px; height:100px;',
            'wysiwyg' => false,
            'required' => false,
        ));

        if (Mage::getSingleton('adminhtml/session')->getHolidayData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getHolidayData());
            Mage::getSingleton('adminhtml/session')->setHolidayData(null);
        } elseif (Mage::registry('holiday_data')) {
            $form->setValues(Mage::registry('holiday_data')->getData());
        }
        return parent::_prepareForm();
    }

}
