<?php

class Simicart_Simihr_Block_Adminhtml_Department_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{

	public function __construct(){
		parent::__construct();
		$this->setId('department_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle($this->__('Department'));
	}
	
	protected function _beforeToHtml(){
		$this->addTab('form_department', array(
			'label'		=> $this->__('Department Infomation'),
			'title'		=> $this->__('Department Infomation'),
			'content' 	=> $this->getLayout()->createBlock('simihr/adminhtml_department_edit_tab_form')->toHtml(),
		));

		$this->addTab('form_jobs_list', array(
			'label'		=> $this->__('Jobs List'),
			'title'		=> $this->__('Jobs List'),
			'content' 	=> $this->getLayout()->createBlock('simihr/adminhtml_department_edit_tab_jobList')->toHtml(),
		));
		
		return parent::_beforeToHtml();
	}
}