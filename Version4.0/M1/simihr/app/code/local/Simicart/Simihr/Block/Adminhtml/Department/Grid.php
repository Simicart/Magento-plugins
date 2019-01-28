<?php
class Simicart_Simihr_Block_Adminhtml_Department_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        // Set some defaults for our grid
        $this->setDefaultSort('id');
        $this->setId('simihr_department_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _getCollectionClass()
    {
        // This is the model we are using for the grid
        return 'simihr/department_collection';
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

        $this->addColumn('name',
            array(
                'header'=> $this->__('Name'),
                'index' => 'name',
                'header_css_class'=>'a-center',
                'align'=>'center'
            )
        );

        $this->addColumn('dep_img',
            array(
                'header'=> $this->__('Image'),
                'width' => '50px',
                'index' => 'dep_img',
                'renderer' => 'Simicart_Simihr_Block_Adminhtml_Renderer_Image',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
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

        $this->addColumn('mobile',
            array(
                'header'=> $this->__('Mobile'),
                'index' => 'mobile',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );

        $this->addColumn('status', array(
            'header'    => $this->__('Status'),
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

        $this->addColumn('sort_order', array(
            'header'    => $this->__('Sort order'),
            'index'     => 'sort_order',
            'header_css_class'=>'a-center',
            'align'=>'center'
        ));

        $this->addColumn('description', array(
            'header'    => $this->__('Description'),
            'index'     => 'description',
            'header_css_class'=>'a-center',
            'align'=>'center',
            'sortable'  => false,
        ));

        $this->addColumn('job_offers_ids', array(
            'header'    => $this->__('Job Offers IDs'),
            'index'     => 'job_offer_ids',
            'header_css_class'=>'a-center',
            'align'=>'center'
        ));
//        $this->addExportType('*/*/exportCsv', $this->__('CSV'));
//        $this->addExportType('*/*/exportExcel', $this->__('Excel'));
//        $this->addExportType('*/*/exportXml', $this->__('XML'));

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