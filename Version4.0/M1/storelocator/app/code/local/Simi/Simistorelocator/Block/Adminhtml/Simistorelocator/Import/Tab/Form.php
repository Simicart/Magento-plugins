<?php

class Simi_Simistorelocator_Block_Adminhtml_Simistorelocator_Import_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
	
      $importFile = Mage::getBaseUrl('media').'simistorelocator/import/sample_import_file.csv';
      $form = new Varien_Data_Form();
	
      $this->setForm($form);
      $fieldset = $form->addFieldset('import_form', array('legend'=>Mage::helper('simistorelocator')->__('Import Settings')));
       $fieldset->addField('', 'select', array(
			'label'		=> Mage::helper('simistorelocator')->__('Overwrite existing store(s)'),
			'required'	=> false,
			'name'		=> 'overwrite_store',
                        'width'         => '50px',
                        'values'        => array(
                            array(
                                    'value'     => 1,
                                    'label'     => Mage::helper('simistorelocator')->__('Yes'),
                                  ),

                             array(
                                    'value'     => 0,
                                    'label'     => Mage::helper('simistorelocator')->__('No'),
                                ),
                        ),
                    
		));
      $fieldset->addField('csv_store', 'file', array(
          'label'     => Mage::helper('simistorelocator')->__('Select File to Import'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'csv_store',
          'note'      => '<a href="'.$importFile.'">Sample File</a>',
      ));
      
      return parent::_prepareForm();
  }
}