<?php
class Simicart_Simihr_Block_Adminhtml_JobOffers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        // Set some defaults for our grid
        $this->setDefaultSort('id');
        $this->setId('simihr_jobOffers_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _getCollectionClass()
    {
        // This is the model we are using for the grid
        return 'simihr/jobOffers_collection';
    }

    protected function _prepareCollection()
    {
        // Get and set our collection for the grid
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        // zend_Debug::dump($collection->getData());die();
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

        $this->addColumn('name',
            array(
                'header'=> $this->__('Name'),
                'width' => '150px',
                'index' => 'name',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'header_css_class'=>'a-center',
                'align'=>'center'
            )
        );

        $this->addColumn('img_url',
            array(
                'header'=> $this->__('Image'),
                'width' => '50px',
                'index' => 'img_url',
                'renderer' => 'Simicart_Simihr_Block_Adminhtml_Renderer_Image',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );

        $this->addColumn('job_type',
            array(
                'header'=> $this->__('Job Type'),
                'width' => '50px',
                'index' => 'job_type',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );

        $this->addColumn('status', array(
            'header'    => $this->__('Status'),
            'width' => '50px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                '1' => $this->__('Enabled'),
                '0' => $this->__('Disabled'),
            ),
            'header_css_class'=>'a-center',
            'align'=>'center',
            'sortable'  => false,
        ));

        $this->addColumn('quatity',
            array(
                'header'=> $this->__('Quatity'),
                'width' => '50px',
                'index' => 'quatity',
                'header_css_class'=>'a-center',
                'align'=>'center'
            )
        );

         $this->addColumn('sort_order_id',
            array(
                'header'=> $this->__('Sort Order ID'),
                'width' => '50px',
                'index' => 'sort_order_id',
                'header_css_class'=>'a-center',
                'align'=>'center'
            )
        );


        $this->addColumn('start_time',
            array(
                'header'=> $this->__('Start time'),
                'width' => '50px',
                'index' => 'start_time',
                'header_css_class'=>'a-center',
                'align'=>'center'
            )
        );

        $this->addColumn('deadline',
            array(
                'header'=> $this->__('Deadline'),
                'width' => '50px',
                'index' => 'deadline',
                'header_css_class'=>'a-center',
                'align'=>'center'
            )
        );

        $this->addColumn('overall',
            array(
                'header'=> $this->__('Overall'),
//                'width' => '50px',
                'index' => 'overall',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );

        $this->addColumn('requirements',
            array(
                'header'=> $this->__('Requirements'),
//                'width' => '50px',
                'index' => 'requirements',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );

        $this->addColumn('work_related',
            array(
                'header'=> $this->__('Work related to'),
//                'width' => '50px',
                'index' => 'work_related',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );

        $this->addColumn('benifits',
            array(
                'header'=> $this->__('Benefits'),
//                'width' => '50px',
                'index' => 'benifits',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );

        $this->addColumn('overall_vn',
            array(
                'header'=> $this->__('Tóm tắt công việc'),
//                'width' => '50px',
                'index' => 'overall_vn',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );

        $this->addColumn('requirements_vn',
            array(
                'header'=> $this->__('Yêu cầu'),
//                'width' => '50px',
                'index' => 'requirements_vn',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );

        $this->addColumn('work_related_vn',
            array(
                'header'=> $this->__('Công việc liên quan'),
//                'width' => '50px',
                'index' => 'work_related_vn',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );

        $this->addColumn('benifits_vn',
            array(
                'header'=> $this->__('Quyền lợi'),
//                'width' => '50px',
                'index' => 'benifits_vn',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        // This is where our row data will link to
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}