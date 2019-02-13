<?php
class Simicart_Simihr_Block_Adminhtml_Content_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        // Set some defaults for our grid
        $this->setDefaultSort('id');
        $this->setId('simihr_content_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _getCollectionClass()
    {
        // This is the model we are using for the grid
        return 'simihr/content_collection';
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
                'align'=>'center',
                'header_css_class'=>'a-center',
                'align'=>'center'
            )
        );


        $this->addColumn('detail',
            array(
                'header'=> $this->__('Detail'),
                'index' => 'detail',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );

        $this->addColumn('detail_vn',
            array(
                'header'=> $this->__('Chi tiết'),
                'index' => 'detail_vn',
                'header_css_class'=>'a-center',
                'align'=>'center',
                'sortable'  => false,
            )
        );

        $this->addColumn('note',
            array(
                'header'=> $this->__('Note'),
                'index' => 'note',
                'header_css_class'=>'a-center',
                'align'=>'center'
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