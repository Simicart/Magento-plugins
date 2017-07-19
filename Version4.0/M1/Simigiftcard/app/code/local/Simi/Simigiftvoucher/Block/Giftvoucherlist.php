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
 * Giftvoucher Giftvoucherlist block
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */
class Simi_Simigiftvoucher_Block_Giftvoucherlist extends Mage_Core_Block_Template
{
    
    protected function _construct()
    {
        parent::_construct();
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $timezone = ((Mage::app()->getLocale()->date()->get(Zend_Date::TIMEZONE_SECS)) / 3600);
        $collection = Mage::getModel('simigiftvoucher/customervoucher')->getCollection()
            ->addFieldToFilter('main_table.customer_id', $customerId);
        $voucherTable = Mage::getModel('core/resource')->getTableName('simigiftvoucher');
        $collection->getSelect()
            ->joinleft(
                array('voucher_table' => $voucherTable), 'main_table.voucher_id = voucher_table.giftvoucher_id', array(
                'recipient_name',
                'gift_code',
                'balance',
                'currency',
                'status',
                'expired_at',
                'customer_check_id' => 'voucher_table.customer_id',
                'recipient_email',
                'customer_email'
            ))
            ->where('voucher_table.status <> ?', Simi_Simigiftvoucher_Model_Status::STATUS_DELETED);
        $collection->getSelect()
            ->columns(array(
                'added_date' => new Zend_Db_Expr("SUBDATE(added_date,INTERVAL " . $timezone . " HOUR)"),
        ));
        $collection->getSelect()
            ->columns(array(
                'expired_at' => new Zend_Db_Expr("SUBDATE(expired_at,INTERVAL " . $timezone . " HOUR)"),
        ));
        $collection->setOrder('customer_voucher_id', 'DESC');
        $this->setCollection($collection);
    }

    /**
     * @return $this
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'giftvoucher_pager')
            ->setTemplate('page/html/pager.phtml')
            ->setCollection($this->getCollection());
        $this->setChild('giftvoucher_pager', $pager);

        $grid = $this->getLayout()->createBlock('simigiftvoucher/grid', 'giftvoucher_grid');
        // prepare column

        $grid->addColumn('gift_code', array(
            'header' => $this->__('Gift Card Code'),
            'index' => 'gift_code',
            'format' => 'medium',
            'align' => 'left',
            'width' => '80px',
            'render' => 'getCodeTxt',
            'searchable' => true,
        ));

        $grid->addColumn('balance', array(
            'header' => $this->__('Balance'),
            'align' => 'left',
            'type' => 'price',
            'index' => 'balance',
            'render' => 'getBalanceFormat',
            'searchable' => true,
        ));
        $statuses = Mage::getSingleton('simigiftvoucher/status')->getOptionArray();
        $grid->addColumn('status', array(
            'header' => $this->__('Status'),
            'align' => 'left',
            'index' => 'status',
            'type' => 'options',
            'options' => $statuses,
            'width' => '50px',
            'searchable' => true,
        ));

        $grid->addColumn('added_date', array(
            'header' => $this->__('Added Date'),
            'index' => 'added_date',
            'type' => 'date',
            'format' => 'medium',
            'align' => 'left',
            'searchable' => true,
        ));
        $grid->addColumn('expired_at', array(
            'header' => $this->__('Expired Date'),
            'index' => 'expired_at',
            'type' => 'date',
            'format' => 'medium',
            'align' => 'left',
            'searchable' => true,
        ));

        $grid->addColumn('action', array(
            'header' => $this->__('Action'),
            'align' => 'left',
            'type' => 'action',
            'width' => '300px',
            'render' => 'getActions',
        ));

        $this->setChild('giftvoucher_grid', $grid);
        return $this;
    }

    /**
     * Get row number
     *
     * @param mixed $row
     * @return string
     */
    public function getNoNumber($row)
    {
        return sprintf('#%d', $row->getId());
    }

    /**
     * Returns the HTML codes of the gift code's column
     *
     * @param mixed $row
     * @return string
     */
    public function getCodeTxt($row)
    {
        $type = Mage::getModel('simigiftvoucher/giftvoucher')->loadByCode($row->getGiftCode());
        if($type->getSetId() > 0){
            $aelement = '<a href="javascript:void(0);" onclick="">'
                . 'XXXXXXXXX' . '</a>';
        }else{
            $aelement = '<a href="javascript:void(0);" onclick="viewgiftcode' . $row->getId() . '()">' .
                Mage::helper('simigiftvoucher')->getHiddenCode($row->getGiftCode()) . '</a>';
        }

        $input = '<input style="width:auto;" id="input-gift-code' . $row->getId() . '" readonly type="text" class="input-text" value="' . 
            $row->getGiftCode() . '" onblur="hiddencode' . $row->getId() . '(this);">';

        $html = '<div id="inputboxgiftvoucher' . $row->getId() . '" >' . $aelement . '</div>
                <script type="text/javascript">
                    //<![CDATA[
                        function viewgiftcode' . $row->getId() . '(){
                            $(\'inputboxgiftvoucher' . $row->getId() . '\').innerHTML=\'' . $input . '\';
                            $(\'input-gift-code' . $row->getId() . '\').focus();
                        }
                        function hiddencode' . $row->getId() . '(el) {
                            $(\'inputboxgiftvoucher' . $row->getId() . '\').innerHTML=\'' . $aelement . '\';
                        }
                    //]]>
                </script>';
        return $html;
    }

    /**
     * Returns the formatted blance
     * 
     * @param mixed $row
     * @return string
     */
    public function getBalanceFormat($row)
    {
        $currency = Mage::getModel('directory/currency')->load($row->getCurrency());
        return $currency->format($row->getBalance());
    }

    /**
     * Returns the HTML codes of the action's column
     * 
     * @param mixed $row
     * @return string
     */
    public function getActions($row)
    {
        $confirmText = Mage::helper('simigiftvoucher')->__('Are you sure?');
        $removeurl = $this->getUrl('simigiftvoucher/index/remove', array('id' => $row->getId()));
        $redeemurl = $this->getUrl('simigiftvoucher/index/redeem', array('giftvouchercode' => $row->getGiftCode()));
        $giftVoucherModel = Mage::getModel('simigiftvoucher/giftvoucher')->load($row->getVoucherId());
        $action = '<a href="' . $this->getUrl('*/*/view', array('id' => $row->getId())) . '">' . 
            $this->__('View') . '</a>';
        // can print gift voucher when status is not used
        if ($row->getStatus() < Simi_Simigiftvoucher_Model_Status::STATUS_DISABLED) {
            //Hai.Tran
            if (!(($row->getStatus() == Simi_Simigiftvoucher_Model_Status::STATUS_PENDING) && ($giftVoucherModel->getSetId() > 0))) {
                $action .= ' | <a href="javascript:void(0);" onclick="window.open(\'' .
                    $this->getUrl('*/*/print', array('id' => $row->getId())) .
                    '\',\'newWindow\', \'width=1000,height=700,resizable=yes,scrollbars=yes\')" >' .
                    $this->__('Print') . '</a>';
            }

            if ($row->getRecipientName() && $row->getRecipientEmail() && ($row->getCustomerId()
                == Mage::getSingleton('customer/session')->getCustomerId() || $row->getCustomerEmail()
                == Mage::getSingleton('customer/session')->getCustomer()->getEmail()) &&
                (!(($row->getStatus() == Simi_Simigiftvoucher_Model_Status::STATUS_PENDING) && ($giftVoucherModel->getSetId() > 0)))
            ) {
                $action .= ' | <a href="' . $this->getUrl('*/*/email', array('id' => $row->getId())) . '">' . 
                    $this->__('Email') . '</a>';
            }
        }
        //

        $avaiable = Mage::helper('simigiftvoucher')
            ->canUseCode($giftVoucherModel);
        if (Mage::helper('simigiftvoucher')->getGeneralConfig('enablecredit') && $avaiable) {
            if (($row->getStatus() == Simi_Simigiftvoucher_Model_Status::STATUS_ACTIVE
                || ($row->getStatus() == Simi_Simigiftvoucher_Model_Status::STATUS_USED
                && $row->getBalance() > 0)) && (!($giftVoucherModel->getSetId() > 0))) {
                $action .=' | <a href="javascript:void(0);" onclick="redeem' . $row->getId() . '()">' .
                    $this->__('Redeem') . '</a>';
                $action .='<script type="text/javascript">
                    //<![CDATA[
                        function redeem' . $row->getId() . '(){
                            if (confirm(\'' . $confirmText . '\')){
                                setLocation(\'' . $redeemurl . '\');
                            }
                        }
                    //]]>
                </script>';
            }
        }
        $action .=' | <a href="javascript:void(0);" onclick="remove' . $row->getId() . '()">' . 
            $this->__('Remove') . '</a>';
        $action .='<script type="text/javascript">
                    //<![CDATA[
                        function remove' . $row->getId() . '(){
                            if (confirm(\'' . $confirmText . '\')){
                                setLocation(\'' . $removeurl . '\');
                            }
                        }
                    //]]>
                </script>';
        return $action;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('giftvoucher_pager');
    }

    /**
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('giftvoucher_grid');
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $this->getChild('giftvoucher_grid')->setCollection($this->getCollection());
        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getBalanceAccount()
    {
        $store = Mage::app()->getStore();
        $creadit = Mage::getModel('simigiftvoucher/credit')->getCreditAccountLogin();
        $currency = Mage::app()->getStore()->getCurrentCurrency();

        return $currency->format($store->convertPrice($creadit->getBalance()));
    }

}
