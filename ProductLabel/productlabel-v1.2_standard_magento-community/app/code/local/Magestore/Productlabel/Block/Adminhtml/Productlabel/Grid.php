<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Productlabel
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Productlabel Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Productlabel
 * @author      Magestore Developer
 */
class Magestore_Productlabel_Block_Adminhtml_Productlabel_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('productlabelGrid');
        $this->setDefaultSort('productlabel_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Productlabel_Block_Adminhtml_Productlabel_Grid
     */
    protected function _prepareCollection() {
        $storeId = $this->getRequest()->getParam('store', 0);
        $collection = Mage::getModel('productlabel/productlabel')->getCollection()->setStoreId($storeId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Productlabel_Block_Adminhtml_Productlabel_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('label_id', array(
            'header' => Mage::helper('productlabel')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'label_id',
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('productlabel')->__('Label Name'),
            'align' => 'left',
            'index' => 'name',
        ));
//
//        $this->addColumn('text', array(
//            'header' => Mage::helper('productlabel')->__('Product Page Text'),
//            'align' => 'left',
//            'index' => 'text',
//        ));

        $this->addColumn('image', array(
            'header' => Mage::helper('productlabel')->__('Image on Product Page'),
            'width' => '80px',
            'align' => 'center',
            'index' => 'image',
            'filter' => false,
            'renderer' => 'Magestore_Productlabel_Block_Adminhtml_Productlabel_Renderimage',
        ));
        $this->addColumn('condition_selected', array(
            'header' => Mage::helper('productlabel')->__('Condition Type'),
            'align' => 'left',
            'width' => '90px',
            'index' => 'condition_selected',
        ));
        $this->addColumn('from_date', array(
            'header' => Mage::helper('productlabel')->__('Start Date'),
            'align' => 'left',
            'width' => '120px',
            'type' => 'date',
            'default' => '--',
            'index' => 'from_date',
        ));

        $this->addColumn('to_date', array(
            'header' => Mage::helper('productlabel')->__('End Date'),
            'align' => 'left',
            'width' => '120px',
            'type' => 'date',
            'default' => '--',
            'index' => 'to_date',
        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('productlabel')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => Mage::helper('productlabel')->__('Active'),
                2 => Mage::helper('productlabel')->__('Inactive'),
            ),
        ));
        $this->addColumn('is_apply', array(
            'header' => Mage::helper('productlabel')->__('Is Applied'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'is_apply',
            'type' => 'options',
            'options' => array(
                1 => Mage::helper('productlabel')->__('Ready'),
                2 => Mage::helper('productlabel')->__('Not Ready'),
            ),
            'renderer' => 'Magestore_Productlabel_Block_Adminhtml_Productlabel_Renderapplied',
        ));
        $this->addColumn('action', array(
            'header' => Mage::helper('productlabel')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('productlabel')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('productlabel')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('productlabel')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Productlabel_Block_Adminhtml_Productlabel_Grid
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('productlabel_id');
        $this->getMassactionBlock()->setFormFieldName('productlabel');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('productlabel')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('productlabel')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('productlabel/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('productlabel')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('productlabel')->__('Status'),
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
        $store = $this->getRequest()->getParam('store');
        return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'store' => $store));
    }

}