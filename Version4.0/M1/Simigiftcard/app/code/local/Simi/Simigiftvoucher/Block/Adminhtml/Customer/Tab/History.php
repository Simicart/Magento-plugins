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
 * Adminhtml Giftvoucher Customer Tab History Block
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Customer_Tab_History extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Simi_Simigiftvoucher_Block_Adminhtml_Customer_Tab_History constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('historyGrid');
        $this->setDefaultSort('history_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     * @throws Exception
     */
    protected function _prepareCollection()
    {
        $customerId = $this->getRequest()->getParam('customer_id');
        
        if (!$customerId) {
            $customerId = Mage::registry('current_customer')->getId();
        }
        $collection = Mage::getModel('simigiftvoucher/credithistory')->getCollection()
            ->addFieldToFilter('customer_id', $customerId);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('history_id', array(
            'header' => Mage::helper('simigiftvoucher')->__('ID'),
            'align' => 'left',
            'width' => '50px',
            'type' => 'number',
            'index' => 'history_id',
        ));
        $this->addColumn('action', array(
            'header' => Mage::helper('simigiftvoucher')->__('Action'),
            'align' => 'left',
            'index' => 'action',
            'type' => 'options',
            'options' => Mage::getSingleton('simigiftvoucher/creditaction')->getOptionArray(),
        ));

        $this->addColumn('balance_change', array(
            'header' => Mage::helper('simigiftvoucher')->__('Balance Change'),
            'align' => 'left',
            'index' => 'balance_change',
            'type' => 'currency',
            'currency' => 'currency',
        ));
        $this->addColumn('giftcard_code', array(
            'header' => Mage::helper('simigiftvoucher')->__('Gift Card Code'),
            'align' => 'left',
            'index' => 'giftcard_code',
        ));
        $this->addColumn('order_number', array(
            'header' => Mage::helper('simigiftvoucher')->__('Order'),
            'align' => 'left',
            'index' => 'order_number',
            'renderer' => 'simigiftvoucher/adminhtml_customer_tab_renderer',
        ));
        $this->addColumn('currency_balance', array(
            'header' => $this->__('Current Balance'),
            'align' => 'left',
            'index' => 'currency_balance',
            'type' => 'currency',
            'currency' => 'currency',
        ));
        $this->addColumn('created_date', array(
            'header' => $this->__('Created Time'),
            'align' => 'left',
            // 'type' => DateTime,
            'type' => 'datetime',
            'index' => 'created_date',
        ));


        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/simigiftvoucher_giftvoucher/history', array(
                '_current' => true,
                'customer_id' => Mage::registry('current_customer')->getId(),
        ));
    }

    /**
     * @param $row
     * @return bool
     */
    public function getRowUrl($row)
    {
        return false;
    }

}
