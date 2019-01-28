<?php
class Simicart_Simihr_Block_Adminhtml_JobOffers_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     */
    public function __construct()
    {  
        parent::__construct();
     
        $this->setId('simihr_jobOffers_form');
        $this->setTitle($this->__('Job Offers Information'));
    }  
     
    /**
     * Setup form fields for inserts/updates
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('simicart_simihr');
     
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method'    => 'post',
            'enctype' => 'multipart/form-data'
        ));
     
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => $this->__('Job Offers Information'),
            'class'     => 'fieldset-wide',
        ));
     
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name' => 'id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => $this->__('Name'),
            'title'     => $this->__('Name'),
            'required'  => true,
        ));

        $fieldset->addField('img_url', 'image', array(
            'name'      => 'img_url',
            'label'     => $this->__('Image'),
            'title'     => $this->__('Image'),
        ));


       $fieldset->addField('job_type', 'select', array(
            'label' => $this->__('Job Type'),
            'name'  => 'job_type',
            'values'=> array(
                array(
                    'value' => 'full-time',
                    'label' => $this->__('Full-time'),
                ),
                array(
                    'value' => 'part-time',
                    'label' => $this->__('Part-time'),
                ),
            ),
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

        $fieldset->addField('quatity', 'text', array(
            'name'      => 'quatity',
            'label'     => $this->__('Quatity'),
            'title'     => $this->__('Quatity'),
        ));

        $fieldset->addField('start_time', 'text', array(
            'name'      => 'start_time',
            'label'     => $this->__('Start time'),
            'title'     => $this->__('Start time'),
            'after_element_html' => '<p>date/month/year</p>',
        ));

        $fieldset->addField('deadline', 'text', array(
            'name'      => 'deadline',
            'label'     => $this->__('Deadline'),
            'title'     => $this->__('Deadline'),
            'after_element_html' => '<p>date/month/year</p>',
        ));

<<<<<<< HEAD
        $fieldset->addField('overall', 'editor', array(
            'name'      => 'overall',
            'label'     => $this->__('Overall'),
            'title'     => $this->__('Overall'),
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'wysiwyg'   => true,
        ));
        $fieldset->addField('overall_vn', 'editor', array(
            'name'      => 'overall_vn',
            'label'     => $this->__('Tóm tắt công việc'),
            'title'     => $this->__('Tóm tắt công việc'),
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'wysiwyg'   => true,
        ));

=======
>>>>>>> 285bf9b53deaeb11971b0ee3c14850d633380d58
        $fieldset->addField('requirements', 'editor', array(
            'name'      => 'requirements',
            'label'     => $this->__('Requirements'),
            'title'     => $this->__('Requirements'),
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'wysiwyg'   => true,
        ));

        $fieldset->addField('requirements_vn', 'editor', array(
            'name'      => 'requirements_vn',
            'label'     => $this->__('Yêu cầu'),
            'title'     => $this->__('Yêu cầu'),
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'wysiwyg'   => true,
        ));

        $fieldset->addField('work_related', 'editor', array(
            'name'      => 'work_related',
            'label'     => $this->__('Work related to'),
            'title'     => $this->__('Work related to'),
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'wysiwyg'   => true,
        ));

        $fieldset->addField('work_related_vn', 'editor', array(
            'name'      => 'work_related_vn',
            'label'     => $this->__('Công việc liên quan tới'),
            'title'     => $this->__('Công việc liên quan tới'),
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'wysiwyg'   => true,
        ));

        $fieldset->addField('benifits', 'editor', array(
            'name'      => 'benifits',
            'label'     => $this->__('Benefits'),
            'title'     => $this->__('Benefits'),
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'wysiwyg'   => true,
        ));

        $fieldset->addField('benifits_vn', 'editor', array(
            'name'      => 'benifits_vn',
            'label'     => $this->__('Quyền lợi'),
            'title'     => $this->__('Quyền lợi'),
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'wysiwyg'   => true,
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
     
        return parent::_prepareForm();
    }  
}