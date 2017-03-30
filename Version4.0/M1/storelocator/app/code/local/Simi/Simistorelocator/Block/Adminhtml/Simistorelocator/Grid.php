<?php

class Simi_Simistorelocator_Block_Adminhtml_Simistorelocator_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('simistorelocatorGrid');
        $this->setDefaultSort('simistorelocator_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Simi_Simistorelocator_Block_Adminhtml_Simistorelocator_Grid
     */
    protected function _prepareCollection() {
        $storeId = $this->getRequest()->getParam('store');
        $collection = Mage::getModel('simistorelocator/simistorelocator')->getCollection();
        if ($storeId) {
             $typeID = Mage::helper('simiconnector')->getVisibilityTypeId('storelocator');
            $visibilityTable = Mage::getSingleton('core/resource')->getTableName('simiconnector/visibility');
            $collection->getSelect()
                ->join(array('visibility' => $visibilityTable), 'visibility.item_id = main_table.simistorelocator_id AND visibility.content_type = ' . $typeID . ' AND visibility.store_view_id =' . $storeId);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
        //get collection from database view on grid;
    }

    /**
     * prepare columns for this grid
     *
     * @return Simi_Simistorelocator_Block_Adminhtml_Simistorelocator_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('simistorelocator_id', array(
            'header' => Mage::helper('simistorelocator')->__('ID'),
            'align' => 'right',
            'width' => '30px',
            'index' => 'simistorelocator_id',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('simistorelocator')->__('Store Name'),
            'align' => 'left',
            'index' => 'name',
            'width' => '150px'
        ));

        $this->addColumn('address', array(
            'header' => Mage::helper('simistorelocator')->__('Address'),
            'width' => '250px',
            'index' => 'address',
        ));

        $this->addColumn('city', array(
            'header' => Mage::helper('simistorelocator')->__('City'),
            'width' => '130px',
            'index' => 'city',
        ));

        $this->addColumn('state', array(
            'header' => Mage::helper('simistorelocator')->__('State/Province'),
            'width' => '130px',
            'index' => 'state',
        ));

        $this->addColumn('country', array(
            'header' => Mage::helper('simistorelocator')->__('Country'),
            'width' => '130px',
            'index' => 'country',
            'type' => 'options',
            'options' => Mage::helper('simistorelocator')->getListCountry(),
        ));

        $this->addColumn('zipcode', array(
            'header' => Mage::helper('simistorelocator')->__('Zip Code'),
            'width' => '130px',
            'index' => 'zipcode',
        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('simistorelocator')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));
        $storeId = $this->getRequest()->getParam('store', 0);
        $this->addColumn('action', array(
            'header' => Mage::helper('simistorelocator')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('simistorelocator')->__('Edit'),
                    'url' => array('base' => '*/*/edit/store/' . $storeId),
                    'field' => 'id'
                )),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('simistorelocator')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('simistorelocator')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return Simi_Simistorelocator_Block_Adminhtml_Simistorelocator_Grid
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('simistorelocator_id');
        $this->getMassactionBlock()->setFormFieldName('simistorelocator');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('simistorelocator')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('simistorelocator')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('simistorelocator/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('simistorelocator')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('simistorelocator')->__('Status'),
                    'values' => $statuses
                ))
        ));
        return $this;
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'store' => $this->getRequest()->getParam('store')));
    }

}
