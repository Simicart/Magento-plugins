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

class Simi_Simigiftvoucher_Block_Adminhtml_Giftcodeset_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Simi_Simigiftvoucher_Block_Adminhtml_Giftcodeset_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('codesetGrid');
        $this->setDefaultSort('set_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('simigiftvoucher/giftcodeset')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('set_id', array(
            'header' => Mage::helper('simigiftvoucher')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'set_id'
        ));

        $this->addColumn('set_name', array(
            'header' => Mage::helper('simigiftvoucher')->__('Set Name'),
            'align' => 'left',
            'index' => 'set_name'
        ));

        $this->addColumn('set_qty', array(
            'header' => Mage::helper('simigiftvoucher')->__('Qty'),
            'align' => 'left',
            'index' => 'set_qty'
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
        $this->setMassactionIdField('set_id');
        $this->getMassactionBlock()->setFormFieldName('giftcodeset');

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


}
