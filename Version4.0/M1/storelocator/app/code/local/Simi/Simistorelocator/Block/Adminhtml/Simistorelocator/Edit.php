<?php
class Simi_Simistorelocator_Block_Adminhtml_Simistorelocator_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct(){
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'simistorelocator';
		$this->_controller = 'adminhtml_simistorelocator';
		
		$this->_updateButton('save', 'label', Mage::helper('simistorelocator')->__('Save Store'));
		$this->_updateButton('delete', 'label', Mage::helper('simistorelocator')->__('Delete Store'));
		
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);

		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('simistorelocator_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'simistorelocator_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'simistorelocator_content');
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
                        
                        function saveApplyForOtherDays(){
                        var status = $('monday_status').value;
                        var open_hour = $('monday_open_hour').value;
                        var open_minute = $('monday_open_minute').value;
                        var close_hour = $('monday_close_hour').value;
                        var close_minute = $('monday_close_minute').value;
                        for(i=0;i<=6;i++) {
                            if( document.getElementsByClassName('status_day')[i])
                                document.getElementsByClassName('status_day')[i].value= status;
                            if(document.getElementsByClassName('hour_open'))
                                 document.getElementsByClassName('hour_open')[i].value= open_hour;
                            if(document.getElementsByClassName('minute_open'))
                                 document.getElementsByClassName('minute_open')[i].value= open_minute;
                            if(document.getElementsByClassName('hour_close'))
                                 document.getElementsByClassName('hour_close')[i].value= close_hour;
                            if(document.getElementsByClassName('minute_close'))
                                 document.getElementsByClassName('minute_close')[i].value= close_minute;
                        }
                       
                    }
		";
	}
	
	/**
	 * get text to show in header when edit an item
	 *
	 * @return string
	 */
	public function getHeaderText(){
		if(Mage::registry('simistorelocator_data') && Mage::registry('simistorelocator_data')->getId())
			return Mage::helper('simistorelocator')->__("Edit store '%s'", $this->htmlEscape(Mage::registry('simistorelocator_data')->getName()));
		return Mage::helper('simistorelocator')->__('Add Store');
	}
}