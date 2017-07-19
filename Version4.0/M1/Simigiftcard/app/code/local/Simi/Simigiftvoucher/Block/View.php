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
 * Giftvoucher View Block
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */
class Simi_Simigiftvoucher_Block_View extends Mage_Core_Block_Template
{

    /**
     * @return mixed
     * @throws Exception
     */
    public function getCustomerGift()
    {
        if (!$this->hasData('customer_gift')) {
            $this->setData('customer_gift', Mage::getModel('simigiftvoucher/customervoucher')->load(
                    $this->getRequest()->getParam('id')
                )
            );
        }
        return $this->getData('customer_gift');
    }

    /**
     * @return mixed
     */
    public function getGiftVoucher()
    {
        if (!$this->hasData('gift_voucher')) {
            $customerGift = $this->getCustomerGift();
            $this->setData('gift_voucher', 
                Mage::getModel('simigiftvoucher/giftvoucher')->load($customerGift->getVoucherId())
            );
        }
        return $this->getData('gift_voucher');
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getGiftVoucherEmail()
    {
        if (!$this->hasData('gift_voucher')) {
            $this->setData('gift_voucher', 
                Mage::getModel('simigiftvoucher/giftvoucher')->load($this->getRequest()->getParam('id'))
            );
        }
        return $this->getData('gift_voucher');
    }

    /**
     * Returns the formatted gift code
     * 
     * @param Simi_Simigiftvoucher_Model_Giftvoucher $giftVoucher
     * @return string
     */
    public function getCodeTxt($giftVoucher)
    {
        return Mage::helper('simigiftvoucher')->getHiddenCode($giftVoucher->getGiftCode());
    }

    /**
     * Returns the formatted balance
     * 
     * @param Simi_Simigiftvoucher_Model_Giftvoucher $giftVoucher
     * @return string
     */
    public function getBalanceFormat($giftVoucher)
    {
        $currency = Mage::getModel('directory/currency')->load($giftVoucher->getCurrency());
        return $currency->format($giftVoucher->getBalance());
    }

    /**
     * Get status of gift code
     * 
     * @param Simi_Simigiftvoucher_Model_Giftvoucher $giftVoucher
     * @return string
     */
    public function getStatus($giftVoucher)
    {
        $status = $giftVoucher->getStatus();
        $statusArray = Mage::getSingleton('simigiftvoucher/status')->getOptionArray();
        return $statusArray[$status];
    }

    /**
     * Check a gift code is sent to the recipient or not
     * 
     * @param Simi_Simigiftvoucher_Model_Giftvoucher $giftCard
     * @return boolean
     */
    public function checkSendFriendGiftCard($giftCard)
    {
        return ($giftCard->getRecipientName() && $giftCard->getRecipientEmail() 
            && $giftCard->getCustomerId() == Mage::getSingleton('customer/session')->getCustomerId());
    }

    /**
     * Get shipment for gift card
     * 
     * @param Simi_Simigiftvoucher_Model_Giftvoucher $giftCard
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getShipmentForGiftCard($giftCard)
    {
        $history = Mage::getResourceModel('simigiftvoucher/history_collection')
            ->addFieldToFilter('giftvoucher_id', $giftCard->getId())
            ->addFieldToFilter('action', Simi_Simigiftvoucher_Model_Actions::ACTIONS_CREATE)
            ->getFirstItem();
        if (!$history->getOrderIncrementId() || !$history->getOrderItemId()) {
            return false;
        }
        $shipmentItem = Mage::getResourceModel('sales/order_shipment_item_collection')
            ->addFieldToFilter('order_item_id', $history->getOrderItemId())
            ->getFirstItem();
        if (!$shipmentItem || !$shipmentItem->getId()) {
            return false;
        }
        $shipment = Mage::getModel('sales/order_shipment')->load($shipmentItem->getParentId());
        if (!$shipment->getId()) {
            return false;
        }
        return $shipment;
    }

    /**
     * Get history for gift card
     * 
     * @param Simi_Simigiftvoucher_Model_Giftvoucher $giftCard
     * @return Simi_Simigiftvoucher_Model_Mysql4_History_Collection
     */
    public function getGiftCardHistory($giftCard)
    {
        $collection = Mage::getResourceModel('simigiftvoucher/history_collection')
            ->addFieldToFilter('main_table.giftvoucher_id', $giftCard->getId());
        if ($giftCard->getCustomerId() != Mage::getSingleton('customer/session')->getCustomerId()) {
            $collection->addFieldToFilter('main_table.customer_id', 
                Mage::getSingleton('customer/session')->getCustomerId());
        }
        $collection->getSelect()->order('main_table.created_at DESC');
        $collection->getSelect()
            ->joinLeft(array('o' => $collection->getTable('sales/order')), 
                'main_table.order_increment_id = o.increment_id', array('order_id' => 'entity_id')
        );
        return $collection;
    }

    /**
     * Get action name of Gift card history
     * 
     * @param Simi_Simigiftvoucher_Model_History $history
     * @return string
     */
    public function getActionName($history)
    {
        $actions = Mage::getSingleton('simigiftvoucher/actions')->getOptionArray();
        if (isset($actions[$history->getAction()])) {
            return $actions[$history->getAction()];
        }
        reset($actions);
        return current($actions);
    }

    /**
     * Returns the formatted amount
     * 
     * @param Simi_Simigiftvoucher_Model_Giftvoucher $giftVoucher
     * @return string
     */
    public function getAmountFormat($giftVoucher)
    {
        $currency = Mage::getModel('directory/currency')->load($giftVoucher->getCurrency());
        return $currency->format($giftVoucher->getAmount());
    }

    /**
     * Returns a Gift Card template object
     * 
     * @param int $templateId
     * @return Simi_Simigiftvoucher_Model_Gifttemplate
     */
    public function getGiftcardTemplate($templateId)
    {
        $templates = Mage::getModel('simigiftvoucher/gifttemplate')->load($templateId);
        return $templates;
    }

    /**
     * @return int
     */
    public function messageMaxLength()
    {
        return (int) Mage::helper('simigiftvoucher')->getInterfaceConfig('max');
    }

}
