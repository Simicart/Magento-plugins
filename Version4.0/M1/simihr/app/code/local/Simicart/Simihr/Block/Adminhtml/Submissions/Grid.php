<?php
class Simicart_Simihr_Block_Adminhtml_Submissions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        // Set some defaults for our grid
        $this->setDefaultSort('id');
        $this->setId('simihr_submissions_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _getCollectionClass()
    {
        // This is the model we are using for the grid
        return 'simihr/submissions_collection';
    }

    protected function _prepareCollection()
    {
        // Get and set our collection for the grid
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        // Add the columns that should appear in the grid
        $this->addColumn('id',
            array(
                'header'=> $this->__('ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'id',
                'header_css_class'=>'a-center',
                'align'=>'center'
            )
        );

        $this->addColumn('first_name',
            array(
                'header'=> $this->__('First Name'),
                'index' => 'first_name',
                'header_css_class'=>'a-center',
                'align'=>'center'
            )
        );

        $this->addColumn('last_name',
            array(
                'header'=> $this->__('Last Name'),
                'index' => 'last_name',
                'header_css_class'=>'a-center',
                'align'=>'center'
            )
        );

        $this->addColumn('email',
            array(
                'header'=> $this->__('Email'),
                'index' => 'email',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );

        $this->addColumn('phone',
            array(
                'header'=> $this->__('Phone'),
                'index' => 'phone',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );

        $this->addColumn('job_applied',
            array(
                'header'=> $this->__('Job Applied'),
                'index' => 'job_applied',
                'header_css_class'=>'a-center',
                'align'=>'center',
            )
        );

        $this->addColumn('comment',
            array(
                'header'=> $this->__('Comment'),
                'index' => 'comment',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );

        $this->addColumn('submitted_at',
            array(
                'header'=> $this->__('Submitted at'),
                'index' => 'submitted_at',
                'header_css_class'=>'a-center',
                'align'=>'center'
            )
        );

        $this->addColumn('resume_cv_path',
            array(
                'header'=>  $this->__('Resume'),
                'width' => '100',
                'index' => 'resume_cv_path',
                'renderer' => 'Simicart_Simihr_Block_Adminhtml_Renderer_Url',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );

        $this->addColumn('cover_letter_path',
            array(
                'header'=> $this->__('Cover Letter'),
                'width' => '100',
                'index' => 'cover_letter_path',
                'renderer' => 'Simicart_Simihr_Block_Adminhtml_Renderer_Url',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );
//

        return parent::_prepareColumns();
    }

    // public function getRowUrl($row)
    // {
    //     // This is where our row data will link to
    //     return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    // }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => $this->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  => $this->__('Are you sure?')
        ));

        return $this;
    }
}