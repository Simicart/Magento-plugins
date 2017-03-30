<?php

class Simi_Simidailydeal_Block_Adminhtml_Simidailydeal_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct(){
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'simidailydeal';
		$this->_controller = 'adminhtml_simidailydeal';
		
		$this->_updateButton('save', 'label', Mage::helper('simidailydeal')->__('Save Item'));
		$this->_updateButton('delete', 'label', Mage::helper('simidailydeal')->__('Delete Item'));
		
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);

		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('simidailydeal_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'simidailydeal_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'simidailydeal_content');
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}

    public function getHeaderText()
    {
        if( Mage::registry('simidailydeal_data') && Mage::registry('simidailydeal_data')->getId() ) {
            if(Mage::registry('simidailydeal_data')->getStatus() == 5)
			{
				return Mage::helper('simidailydeal')->__("Deal Infomation");			
			} else {
				return Mage::helper('simidailydeal')->__("Edit Deal for '%s'", $this->htmlEscape(Mage::registry('simidailydeal_data')->getProductName()));
			}
		} else {
            return Mage::helper('simidailydeal')->__('Add Deal');
        }
    }
    public function getSimidailydeal()     
    { 
        if (!$this->hasData('simidailydeal_data')) 
		{
            $this->setData('simidailydeal_data', Mage::registry('simidailydeal_data'));
        }
        return $this->getData('simidailydeal_data');
    }
}