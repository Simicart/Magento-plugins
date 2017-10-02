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
 * Adminhtml Giftvoucher Generategiftcard Edit Tab Giftcodelist Block
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */

class Simi_Simigiftvoucher_Block_Adminhtml_Generategiftcard_Edit_Tab_Giftcodelist 
    extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Simi_Simigiftvoucher_Block_Adminhtml_Generategiftcard_Edit_Tab_Giftcodelist constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('giftcodelistGrid');
        $this->setDefaultSort('giftvoucher_id');
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
        $id = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('simigiftvoucher/giftvoucher')->getCollection()
            ->addFieldToFilter('template_id', $id)
            ->joinHistory();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('giftvoucher_id', array(
            'header' => Mage::helper('simigiftvoucher')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'giftvoucher_id',
            'filter_index' => 'main_table.giftvoucher_id'
        ));

        $this->addColumn('gift_code', array(
            'header' => Mage::helper('simigiftvoucher')->__('Gift Card Code'),
            'align' => 'left',
            'index' => 'gift_code',
            'filter_index' => 'main_table.gift_code'
        ));

        $this->addColumn('history_amount', array(
            'header' => Mage::helper('simigiftvoucher')->__('Initial value'),
            'align' => 'left',
            'index' => 'history_amount',
            'type' => 'currency',
            'currency' => 'history_currency',
            'filter_index' => 'history.amount'
        ));

        $this->addColumn('balance', array(
            'header' => Mage::helper('simigiftvoucher')->__('Current balance'),
            'align' => 'left',
            'index' => 'balance',
            'type' => 'currency',
            'currency' => 'currency',
            'filter_index' => 'main_table.balance'
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('simigiftvoucher')->__('Status'),
            'align' => 'left',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('simigiftvoucher/status')->getOptionArray(),
            'filter_index' => 'main_table.status'
        ));


        $this->addColumn('created_at', array(
            'header' => Mage::helper('simigiftvoucher')->__('Created at'),
            'align' => 'left',
            'index' => 'created_at',
            'type' => 'datetime',
            'filter_index' => 'history.created_at'
        ));

        $this->addColumn('expired_at', array(
            'header' => Mage::helper('simigiftvoucher')->__('Expired at'),
            'align' => 'left',
            'index' => 'expired_at',
            'type' => 'datetime',
            'filter_index' => 'main_table.expired_at'
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

        $this->addExportType('*/*/exportGiftCodeCsv', Mage::helper('simigiftvoucher')->__('CSV'));
        $this->addExportType('*/*/exportGiftCodeXml', Mage::helper('simigiftvoucher')->__('XML'));
        
        if (!Mage::helper('simigiftvoucher')->isShowOnlySimpleTemplate()) {
            $this->addExportType('*/*/exportGiftCodePdf', Mage::helper('simigiftvoucher')->__('PDF'));
        }
        return parent::_prepareColumns();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getCsv()
    {
        $csv = '';
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->getCollection()->load();
        $this->_afterLoadCollection();

        $this->addColumn('currency', array('index' => 'currency'));
        $this->addColumn('customer_id', array('index' => 'customer_id'));
        $this->addColumn('customer_email', array('index' => 'customer_email'));
        $this->addColumn('recipient_email', array('index' => 'recipient_email'));
        $this->addColumn('recipient_address', array('index' => 'recipient_address'));
        $this->addColumn('message', array('index' => 'message'));
        $this->addColumn('history_currency', array('index' => 'history_currency'));

        $data = array();
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $data[] = '"' . $column->getIndex() . '"';
            }
        }

        $csv .= implode(',', $data) . "\n";

        foreach ($this->getCollection() as $item) {
            $data = array();
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"' . str_replace(array('"', '\\', chr(13), chr(10)), array('""', '\\\\', '', '\n'), 
                        $item->getData($column->getIndex())) . '"';
                }
            }
            $csv .= implode(',', $data) . "\n";
        }

        if ($this->getCountTotals()) {
            $data = array();
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'), 
                        $column->getRowFieldExport($this->getTotals())) . '"';
                }
            }
            $csv.= implode(',', $data) . "\n";
        }

        return $csv;
    }

    /**
     * @return mixed
     */
    public function getPdf()
    {
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $giftvoucherIds = $this->getCollection()->load()->getAllIds();
        $pdf = Mage::getModel('simigiftvoucher/pdf_giftvoucher')->getPdf($giftvoucherIds);
        return $pdf;
    }

    /**
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/simigiftvoucher_giftvoucher/edit', array('id' => $row->getId()));
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

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/giftcodelist', array(
                '_current' => true,
        ));
    }

}
