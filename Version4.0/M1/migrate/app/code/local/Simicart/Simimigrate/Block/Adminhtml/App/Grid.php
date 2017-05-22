<?php

/**

 */
class Simicart_Simimigrate_Block_Adminhtml_App_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('appGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('simimigrate/app')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('app_id', array(
            'header' => Mage::helper('simimigrate')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'entity_id',
        ));
        
        $this->addColumn('simicart_app_config_id', array(
            'header' => Mage::helper('simimigrate')->__('Simicart App Id'),
            'width' => '150px',
            'index' => 'simicart_app_config_id'
        ));
        
        $this->addColumn('simicart_customer_id', array(
            'header' => Mage::helper('simimigrate')->__('Simicart User Id'),
            'width' => '150px',
            'index' => 'simicart_customer_id'
        ));
        
        $this->addColumn('user_email', array(
            'header' => Mage::helper('simimigrate')->__('Simicart User Email'),
            'width' => '150px',
            'index' => 'user_email'
        ));
        
        $this->addColumn('user_name', array(
            'header' => Mage::helper('simimigrate')->__('User Name'),
            'width' => '250px',
            'index' => 'user_name'
        ));
        
        $this->addColumn('website_url', array(
            'header' => Mage::helper('simimigrate')->__('Website Url'),
            'width' => '150px',
            'index' => 'website_url'
        ));
        
        $this->addColumn('action', array(
            'header' => Mage::helper('simimigrate')->__('Action'),
            'width' => '80px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('simimigrate')->__('View'),
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
        $this->setMassactionIdField('app_id');
        $this->getMassactionBlock()->setFormFieldName('app_id');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('simimigrate')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('simimigrate')->__('Are you sure?')
        ));

        return $this;
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('entity_id' => $row->getId()));
    }

}
