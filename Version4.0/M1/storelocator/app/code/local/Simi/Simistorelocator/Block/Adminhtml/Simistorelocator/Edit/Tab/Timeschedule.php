<?php

class Simi_Simistorelocator_Block_Adminhtml_Simistorelocator_Edit_Tab_Timeschedule extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getStoreData()) {
            $data = Mage::getSingleton('adminhtml/session')->getStoreData();
            Mage::getSingleton('adminhtml/session')->setStoreData(null);
        } elseif (Mage::registry('simistorelocator_data')) {
            $data = Mage::registry('simistorelocator_data')->getData();
        }
//print_r($data);exit;
        $timeInterval = array();
        foreach (array(15, 30, 45) as $key => $var) {
            $timeInterval[$key]['value'] = $var;
            $timeInterval[$key]['label'] = Mage::helper('simistorelocator')->__($var . ' minutes');
        }

        $html_button = '<button style="float:right" onclick="saveApplyForOtherDays()" class="scalable save" type="button" title="Apply for other days" id="id_apply"><span>'.Mage::helper('simistorelocator')->__('Apply to other days').'</span></button><style>.entry-edit .entry-edit-head h4{width:100%;}</style>';
        foreach (array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') as $key => $day) {
            if ($key == 0)
                $fieldset = $form->addFieldset('timeschedule_form_' . $day, array('legend' => Mage::helper('simistorelocator')->__(ucfirst($day) . $html_button)));
            else
                $fieldset = $form->addFieldset('timeschedule_form_' . $day, array('legend' => Mage::helper('simistorelocator')->__(ucfirst($day))));
            $fieldset->addField($day . '_status', 'select', array(
                'label' => Mage::helper('simistorelocator')->__('Open'),
                'required' => false,
                'name' => $day . '_status',
                'class' => 'status_day',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('simistorelocator')->__('Yes'),
                    ),
                    array(
                        'value' => 2,
                        'label' => Mage::helper('simistorelocator')->__('No'),
                    ),
                )
            ));


            $field = array('name' => $day . '_open',
                'data' => isset($data[$day . '_open']) ? $data[$day . '_open'] : '',
                'type' => 'open'
            );
            $fieldset->addField($day . '_open', 'note', array(
                'label' => Mage::helper('simistorelocator')->__('Open Time'),
                'name' => $day . '_open',
                'text' => $this->getLayout()->createBlock('simistorelocator/adminhtml_time')->setData('field', $field)->setTemplate('simistorelocator/time.phtml')->toHtml(),
            ));

            $field = array('name' => $day . '_close',
                'data' => isset($data[$day . '_close']) ? $data[$day . '_close'] : '',
                'type' => 'close'
            );
            $fieldset->addField($day . '_close', 'note', array(
                'label' => Mage::helper('simistorelocator')->__('Close Time'),
                'name' => $day . '_close',
                'text' => $this->getLayout()->createBlock('simistorelocator/adminhtml_time')->setData('field', $field)->setTemplate('simistorelocator/time.phtml')->toHtml(),
            ));
         
        }

        if (Mage::getSingleton('adminhtml/session')->getStoreData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getStoreData());
            Mage::getSingleton('adminhtml/session')->setStoreData(null);
        } elseif (Mage::registry('simistorelocator_data')) {
            $form->setValues(Mage::registry('simistorelocator_data')->getData());
        }
        return parent::_prepareForm();
    }

}