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
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tab_History
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tab_History extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Simi_Simigiftvoucher_Block_Adminhtml_Giftvoucher_Edit_Tab_History constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->setId('historyGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('simigiftvoucher/history')
                ->getCollection()
                ->addFieldToFilter('giftvoucher_id', $this->getGiftvoucher());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns() {
        $this->addColumn('created_at', array(
            'header' => Mage::helper('simigiftvoucher')->__('Created at'),
            'align' => 'left',
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '160px',
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('simigiftvoucher')->__('Action'),
            'align' => 'left',
            'index' => 'action',
            'type' => 'options',
            'options' => Mage::getSingleton('simigiftvoucher/actions')->getOptionArray(),
        ));

        $this->addColumn('amount', array(
            'header' => Mage::helper('simigiftvoucher')->__('Value'),
            'align' => 'left',
            'index' => 'amount',
            'type' => 'currency',
            'currency' => 'currency',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('simigiftvoucher')->__('Status'),
            'align' => 'left',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('simigiftvoucher/status')->getOptionArray(),
        ));

        $this->addColumn('order_increment_id', array(
            'header' => Mage::helper('simigiftvoucher')->__('Order'),
            'align' => 'left',
            'index' => 'order_increment_id',
            'renderer' => 'simigiftvoucher/adminhtml_giftvoucher_renderer_order',
        ));

        $this->addColumn('comments', array(
            'header' => Mage::helper('simigiftvoucher')->__('Comments'),
            'align' => 'left',
            'index' => 'comments',
        ));

        $this->addColumn('extra_content', array(
            'header' => Mage::helper('simigiftvoucher')->__('Action Created by'),
            'align' => 'left',
            'index' => 'extra_content',
        ));

        return parent::_prepareColumns();
    }

    /**
     * @param $row
     * @return bool
     */
    public function getRowUrl($row) {
        return false;
    }

    /**
     * @return string
     */
    public function getGridUrl() {
        return $this->getUrl('*/*/historygrid', array(
                    '_current' => true,
                    'id' => $this->getGiftvoucher(),
        ));
    }

}