<?php
/**
 * Simi
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Simicart.com license that is
 * available through the world-wide-web at this URL:
 * http://www.Simicart.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Simi
 * @package     Simi_Simigiftvoucher
 * @module     Giftvoucher
 * @author      Simi Developer
 *
 * @copyright   Copyright (c) 2016 Simi (http://www.Simicart.com/)
 * @license     http://www.Simicart.com/license-agreement.html
 *
 */

/**
 * Adminhtml Giftvoucher Generategiftcard Grid Block
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */

class Simi_Simigiftvoucher_Block_Adminhtml_Generategiftcard_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Simi_Simigiftvoucher_Block_Adminhtml_Generategiftcard_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('templateGrid');
        $this->setDefaultSort('template_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('simigiftvoucher/template')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('template_id', array(
            'header' => Mage::helper('simigiftvoucher')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'template_id'
        ));

        $this->addColumn('template_name', array(
            'header' => Mage::helper('simigiftvoucher')->__('Pattern Name'),
            'align' => 'left',
            'index' => 'template_name'
        ));

        $this->addColumn('pattern', array(
            'header' => Mage::helper('simigiftvoucher')->__('Pattern'),
            'align' => 'left',
            'index' => 'pattern'
        ));

        $this->addColumn('balance', array(
            'header' => Mage::helper('simigiftvoucher')->__('Balance'),
            'align' => 'left',
            'index' => 'balance',
            'type' => 'currency',
            'currency' => 'currency'
        ));

        $this->addColumn('currency', array(
            'header' => Mage::helper('simigiftvoucher')->__('Currency'),
            'align' => 'left',
            'index' => 'currency',
        ));

        $this->addColumn('amount', array(
            'header' => Mage::helper('simigiftvoucher')->__('Gift Code Qty'),
            'align' => 'left',
            'index' => 'amount',
            'type' => 'number'
        ));

        $this->addColumn('store_id', array(
            'header' => Mage::helper('simigiftvoucher')->__('Store view'),
            'align' => 'left',
            'index' => 'store_id',
            'type' => 'store',
            'store_all' => true,
            'store_view' => true,
            'filter_index' => 'main_table.store_id',
            'skipEmptyStoresLabel' => true,
            'filter_condition_callback' => array($this, 'filterByGiftvoucherStoreId')
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('simigiftvoucher')->__('Action'),
            'width' => '70px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('simigiftvoucher')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('simigiftvoucher')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('simigiftvoucher')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('template_id');
        $this->getMassactionBlock()->setFormFieldName('template');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('simigiftvoucher')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('simigiftvoucher')->__('Are you sure?')
        ));

        return $this;
    }

    /**
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * @param $collection
     * @param $column
     */
    public function filterByGiftvoucherStoreId($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if (isset($value) && $value) {
            $collection->addFieldToFilter('main_table.store_id', $value);
        }
    }

}
