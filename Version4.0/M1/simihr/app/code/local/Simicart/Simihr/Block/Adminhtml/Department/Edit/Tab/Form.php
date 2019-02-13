<?php

class Simicart_Simihr_Block_Adminhtml_Department_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form{

    /**
     * Init class
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('simihr_department_form');
        $this->setTitle($this->__('Department Information'));
    }

    protected function _prepareForm(){

		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('department');
		$form->setFieldNameSuffix('department');
		$this->setForm($form);
		$fieldset = $form->addFieldset('department_form', array('legend'=>$this->__('Department')));
        $model = Mage::registry('simicart_simihr');
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name' => 'id',
            ));
        }
		$fieldset->addField('name', 'text', array(
			'label' => $this->__('Department Name'),
			'name'  => 'name',
			'required'  => true,
			'class' => 'required-entry',

		));

        $fieldset->addField('dep_img', 'image', array(
            'name'      => 'dep_img',
            'label'     => $this->__('Image'),
            'title'     => $this->__('Image'),
        ));

		$fieldset->addField('email', 'text', array(
			'label' => $this->__('Email'),
			'name'  => 'email',

		));

		$fieldset->addField('mobile', 'text', array(
			'label' => $this->__('Mobile'),
			'name'  => 'mobile',
			'required'  => true,
			'class' => 'required-entry',

		));

		$fieldset->addField('status', 'select', array(
            'label' => $this->__('Status'),
            'name'  => 'status',
            'values'=> array(
                array(
                    'value' => 1,
                    'label' => $this->__('Enabled'),
                ),
                array(
                    'value' => 0,
                    'label' => $this->__('Disabled'),
                ),
            ),
        ));

        $fieldset->addField('description', 'text', array(
            'name'      => 'description',
            'label'     => $this->__('Description'),
            'title'     => $this->__('Description'),
            'required'  => true,

        ));

        $fieldset->addField('sort_order', 'text', array(
            'name'      => 'sort_order',
            'label'     => $this->__('Sort order'),
            'title'     => $this->__('Sort order')

        ));

        $fieldset->addField('job_offer_ids', 'text', array(
            'name'      => 'job_offer_ids',
            'label'     => $this->__('Job Offers IDs'),
            'title'     => $this->__('Job Offers IDs'),
            'after_element_html' => '',
        ));



        $form->setValues($model->getData());

        $this->setForm($form);
		
		return parent::_prepareForm();
	}
}