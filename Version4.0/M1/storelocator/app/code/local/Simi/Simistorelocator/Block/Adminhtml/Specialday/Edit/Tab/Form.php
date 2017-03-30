<?php

class Simi_Simistorelocator_Block_Adminhtml_Specialday_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        if (Mage::getSingleton('adminhtml/session')->getSpecialdayData()) {
            $data = Mage::getSingleton('adminhtml/session')->getSpecialdayData();
            Mage::getSingleton('adminhtml/session')->setSpecialdayData(null);
        } elseif (Mage::registry('specialday_data')) {
            $data = Mage::registry('specialday_data')->getData();
        }
      
                    
       
        $fieldset = $form->addFieldset('specialday_form', array('legend' => Mage::helper('simistorelocator')->__('Special Day Information')));
        
        $html = Mage::helper('simistorelocator')->__('<span style="color:#EA4909">Note: Special days will be given the highest priority compared with Holidays and other days.</span>');
        $fieldset->addField('guide', 'note', array(            
            'name' => 'guide',
            'text' => $html,
        ));
       
        
        $image_calendar = Mage::getBaseUrl('skin') . 'adminhtml/default/default/images/grid-cal.gif';
        $fieldset->addField('store_id', 'multiselect', array(
            'label' => Mage::helper('simistorelocator')->__('Store'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'store_id',
            'values' => Mage::helper('simistorelocator')->getStoreOptions(),
            'note'  => Mage::helper('simistorelocator')->__('Select stores applied special day(s)'),
        ));

        $fieldset->addField('date', 'date', array(
            'label' => Mage::helper('simistorelocator')->__('From Date'),
            'required' => true,
            'format' => 'yyyy-MM-dd',
            'image' => $image_calendar,
            'name' => 'date',
        ));

        $fieldset->addField('specialday_date_to', 'date', array(
            'label' => Mage::helper('simistorelocator')->__('To Date'),
            'required' => true,
            'format' => 'yyyy-MM-dd',
            'image' => $image_calendar,
            'name' => 'specialday_date_to',
        ));
        $timeInterval = array();
        foreach (array(15, 30, 45) as $key => $var) {
            $timeInterval[$key]['value'] = $var;
            $timeInterval[$key]['label'] = Mage::helper('simistorelocator')->__($var . ' minutes');
        }
        

        $field_open = array('name' => 'specialday_time_open',
            'data' => isset($data['specialday_time_open']) ? $data['specialday_time_open'] : ''
        );
        $fieldset->addField('specialday_time_open', 'note', array(
            'label' => Mage::helper('simistorelocator')->__('Open Time'),
            'name' => 'specialday_time_open',
            'text' => $this->getLayout()->createBlock('simistorelocator/adminhtml_time')->setData('field', $field_open)->setTemplate('simistorelocator/time.phtml')->toHtml(),
        ));

        $field_close = array('name' => 'specialday_time_close',
            'data' => isset($data['specialday_time_close']) ? $data['specialday_time_close'] : ''
        );
        $fieldset->addField('specialday_time_close', 'note', array(
            'label' => Mage::helper('simistorelocator')->__('Close Time'),
            'name' => 'specialday_time_close',
            'text' => $this->getLayout()->createBlock('simistorelocator/adminhtml_time')->setData('field', $field_close)->setTemplate('simistorelocator/time.phtml')->toHtml(),
        ));

        $fieldset->addField('comment', 'textarea', array(
            'name' => 'comment',
            'label' => Mage::helper('simistorelocator')->__('Comment'),
            'title' => Mage::helper('simistorelocator')->__('Comment'),
            //'note' => Mage::helper('storepickup')->__('Message to customers'),
            'style' => 'width:500px; height:100px;',
            'wysiwyg' => false,
            'required' => false,
        ));

        if (Mage::getSingleton('adminhtml/session')->getSpecialdayData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getSpecialdayData());
            Mage::getSingleton('adminhtml/session')->setSpecialdayData(null);
        } elseif (Mage::registry('specialday_data')) {
            $form->setValues(Mage::registry('specialday_data')->getData());
        }
        return parent::_prepareForm();
    }

}
