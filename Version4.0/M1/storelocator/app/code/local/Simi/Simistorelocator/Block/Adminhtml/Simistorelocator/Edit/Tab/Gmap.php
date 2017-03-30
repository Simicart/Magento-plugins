<?php

    class Simi_Simistorelocator_Block_Adminhtml_Simistorelocator_Edit_Tab_Gmap extends Mage_Adminhtml_Block_Widget_Form{        
        
        protected function _prepareForm()
        {            
            $form = new Varien_Data_Form();
            $this->setForm($form);
            if (Mage::getSingleton('adminhtml/session')->getSimistorelocatorData()) {
                $data = Mage::getSingleton('adminhtml/session')->getSimistorelocatorData();
                Mage::getSingleton('adminhtml/session')->setSimistorelocatorData(null);
            } elseif (Mage::registry('simistorelocator_data'))
                $data = Mage::registry('simistorelocator_data')->getData();
            
            $fieldset = $form->addFieldset('simistorelocator_form', array('legend'=>Mage::helper('simistorelocator')->__('Google Map')));    
            $fieldset->addField('zoom_level', 'text', array(
                'label'     => Mage::helper('simistorelocator')->__('Zoom Level'),                
                 'name'      => 'zoom_level',
             ));
            $fieldset->addField('latitude', 'text', array(
                'label'     => Mage::helper('simistorelocator')->__('Store Latitude'),                
                 'name'      => 'latitude',
             ));
            $fieldset->addField('longtitude', 'text', array(
                'label'     => Mage::helper('simistorelocator')->__('Store Longitude'),                
                 'name'      => 'longtitude',
             ));
          
            if(isset($data['image_icon']) && $data['image_icon']){
                $data['image_icon'] = 'simistorelocator/images/icon/' . $data['simistorelocator_id'] . '/' . $data['image_icon'];
            }                     
            $fieldset->addField('image_icon', 'image', array(
                'label'     => Mage::helper('simistorelocator')->__('Store Icon'),     
                'note'      => 'Shown on Google Map',
                'name'      => 'image_icon',		 
            ));
            $fieldset->addField('gmap', 'text', array(	
                'label'     => Mage::helper('simistorelocator')->__('Store Map'), 
                'name'		=> 'gmap',                       
            ))->setRenderer($this->getLayout()->createBlock('simistorelocator/adminhtml_gmap'));     
            
            
             $form->setValues($data);
             
            return parent::_prepareForm();
          }
    }