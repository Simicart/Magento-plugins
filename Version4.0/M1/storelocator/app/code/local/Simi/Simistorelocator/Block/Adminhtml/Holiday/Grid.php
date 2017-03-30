<?php

class Simi_Simistorelocator_Block_Adminhtml_Holiday_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('holidayGrid');
        $this->setDefaultSort('simistorelocator_holiday_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        
        $collection = Mage::getModel('simistorelocator/holiday')->getCollection();
        $filter   = $this->getParam($this->getVarNameFilter(), null);
        $condorder = '';
        $condorderto = '';
        if($filter){
            $data = $this->helper('adminhtml')->prepareFilterString($filter);
            
            foreach($data as $value=>$key){               
                if($value == 'date'){
                    $condorder = $key;
                }
                if($value == 'holiday_date_to')
                {
                       $condorderto = $key;               
                }
            }
        }
        if($condorder){
            $condorder = Mage::helper('simistorelocator')->filterDates($condorder,array('from','to'));
            $from = $condorder['from'];
            $to = $condorder['to'];
            if($from){
                $from = date('Y-m-d',strtotime($from));
                $collection->addFieldToFilter('date',array('gteq'=>$from));
            }
            if($to){
                $to = date('Y-m-d',strtotime($to));
                $to .= ' 23:59:59';
                $collection->addFieldToFilter('date',array('lteq'=>$to));
            }
        }
        if($condorderto){
            $condorderto = Mage::helper('simistorelocator')->filterDates($condorderto,array('from','to'));
            $fromto = $condorderto['from'];
            $toto = $condorderto['to'];
            if($fromto){
                $fromto = date('Y-m-d',strtotime($fromto));
                $collection->addFieldToFilter('holiday_date_to',array('gteq'=>$fromto));
            }
            if($toto){
                $toto = date('Y-m-d',strtotime($toto));
                $toto .= ' 23:59:59';
                $collection->addFieldToFilter('holiday_date_to',array('lteq'=>$toto));
            }
        }
        
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('simistorelocator_holiday_id', array(
            'header' => Mage::helper('simistorelocator')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'simistorelocator_holiday_id',
        ));
        
        $holidayoptions = Mage::helper('simistorelocator')->getSpecialdayOption();
        
        $this->addColumn('store_id', array(
            'header' => Mage::helper('simistorelocator')->__('Store'),
            'align' => 'left',
            'width' => '300',     
            'index' => 'store_id',
            'type'      =>  'options',
            'options'   =>  $holidayoptions,
            'renderer' => 'Simi_Simistorelocator_Block_Adminhtml_Holiday_Renderer_Store',
            'filter_condition_callback' => array($this, 'filterCallback'),
        ));

        $this->addColumn('date', array(
            'header' => Mage::helper('simistorelocator')->__('From Date'),
            'align' => 'left',
            'width' => '200',
            'type' => 'date',
            'format' => 'F',
            'index' => 'date',
            'filter_condition_callback' => array($this, 'filterCreatedOn')
        ));

        $this->addColumn('holiday_date_to', array(
            'header' => Mage::helper('simistorelocator')->__('To Date'),
            'align' => 'left',
            'width' => '200',
            'type' => 'date',
            'format' => 'F',
            'index' => 'holiday_date_to',
            'filter_condition_callback' => array($this, 'filterCreatedOn')
        ));


        $this->addColumn('comment', array(
            'header' => Mage::helper('simistorelocator')->__('Comment'),
            'index' => 'comment',
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('simistorelocator')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('simistorelocator')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('simistorelocator')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('simistorelocator')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('simistorelocator_holiday_id');
        $this->getMassactionBlock()->setFormFieldName('holiday');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('simistorelocator')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('simistorelocator')->__('Are you sure?')
        ));

        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
    public function filterCreatedOn($collection, $column)
    {
        return $this;
    }

}
