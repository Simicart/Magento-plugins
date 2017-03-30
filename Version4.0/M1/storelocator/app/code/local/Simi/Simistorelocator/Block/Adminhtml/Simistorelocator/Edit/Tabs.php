<?php
class Simi_Simistorelocator_Block_Adminhtml_Simistorelocator_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('simistorelocator_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('simistorelocator')->__('Store Information'));
	}
	
	/**
	 * prepare before render block to html
	 *
	 * @return Simi_Simistorelocator_Block_Adminhtml_Simistorelocator_Edit_Tabs
	 */
	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('simistorelocator')->__('General Information'),
			'title'	 => Mage::helper('simistorelocator')->__('General Information'),
			'content'	 => $this->getLayout()->createBlock('simistorelocator/adminhtml_simistorelocator_edit_tab_generalinfo')->toHtml(),
		));
               //if ($this->getRequest()->getParam('id')) {
			$this->addTab('gmap_section', array(
                            'label'     => Mage::helper('simistorelocator')->__('Google Map'),
                            'title'     => Mage::helper('simistorelocator')->__('Google Map'),
                           'content'   => $this->getLayout()->createBlock('simistorelocator/adminhtml_simistorelocator_edit_tab_gmap')->toHtml(),
                            
			));	  
                 //}
             $this->addTab('timeschedule_section', array(
                'label' => Mage::helper('simistorelocator')->__('Time Schedule'),
                'title' => Mage::helper('simistorelocator')->__('Time Schedule'),
                'content' => $this->getLayout()->createBlock('simistorelocator/adminhtml_simistorelocator_edit_tab_timeschedule')->toHtml(),
            ));
		return parent::_beforeToHtml();
	}
}