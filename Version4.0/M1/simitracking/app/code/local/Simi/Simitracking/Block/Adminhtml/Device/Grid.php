<?php

/**

 */
class Simi_Simitracking_Block_Adminhtml_Device_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('deviceGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('simitracking/device')->getCollection()->addFieldToFilter('is_key_token','0');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     */
    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('simitracking')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'entity_id',
        ));

        $this->addColumn('user_email', array(
            'header' => Mage::helper('simitracking')->__('Customer Email'),
            'width' => '150px',
            'index' => 'user_email'
        ));

        $this->addColumn('plaform_id', array(
            'header' => Mage::helper('simitracking')->__('Device Type'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'plaform_id',
            'type' => 'options',
            'options' => array(
                3 => Mage::helper('simitracking')->__('Android'),
                1 => Mage::helper('simitracking')->__('iPhone'),
                2 => Mage::helper('simitracking')->__('iPad'),
            ),
        ));
        
        $this->addColumn('device_user_agent', array(
            'header' => Mage::helper('simitracking')->__('Device User Agent'),
            'width' => '150px',
            'index' => 'device_user_agent'
        ));

        $this->addColumn('created_time', array(
            'header' => Mage::helper('simitracking')->__('Created Date'),
            'width' => '150px',
            'align' => 'right',
            'index' => 'created_time',
            'type' => 'datetime'
        ));
        
        
        
        $this->addColumn('action', array(
            'header' => Mage::helper('simitracking')->__('Action'),
            'width' => '80px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('simitracking')->__('View'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Madapter_Block_Adminhtml_Madapter_Grid
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('simitracking');
        $this->getMassactionBlock()->setFormFieldName('simitracking');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('simitracking')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('simitracking')->__('Are you sure?')
        ));

        return $this;
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
