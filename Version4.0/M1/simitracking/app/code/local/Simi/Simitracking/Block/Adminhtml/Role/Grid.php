<?php

/**

 */
class Simi_Simitracking_Block_Adminhtml_Role_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('noticeGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $webId = 0;
        $collection = Mage::getModel('simitracking/role')->getCollection();
        if ($this->getRequest()->getParam('website')) {
            $webId = $this->getRequest()->getParam('website');
            $collection->addFieldToFilter('website_id', array('eq' => $webId));
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('simitracking')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'entity_id',
        ));

        $this->addColumn('role_title', array(
            'header' => Mage::helper('simitracking')->__('Role Title'),
            'align' => 'left',
            'index' => 'role_title',
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('simitracking');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('simitracking')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('simitracking')->__('Are you sure?')
        ));

        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
