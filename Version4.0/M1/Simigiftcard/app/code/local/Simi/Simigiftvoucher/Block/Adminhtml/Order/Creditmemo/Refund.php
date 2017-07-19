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
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Order_Creditmemo_Refund
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Order_Creditmemo_Refund extends Mage_Adminhtml_Block_Template {

    /**
     * @return mixed
     */
    public function getCreditmemo() {
        return Mage::registry('current_creditmemo');
    }

    /**
     * @return mixed
     */
    public function getOrder() {
        return $this->getCreditmemo()->getOrder();
    }

    /**
     * @return bool|Mage_Core_Model_Abstract
     */
    public function getCustomer() {
        $order = $this->getOrder();
        if ($order->getCustomerIsGuest()) {
            return false;
        }
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        if ($customer->getId()) {
            return $customer;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function getIsShow() {
        return ($this->getCreditmemo()->getSimiuseGiftCreditAmount() || $this->getCreditmemo()->getSimigiftVoucherDiscount());
    }

    /**
     * @return float
     */
    public function getMaxAmount() {
        $maxAmount = 0;
        if ($this->getCreditmemo()->getSimiuseGiftCreditAmount() && Mage::helper('simigiftvoucher')->getGeneralConfig('enablecredit', $this->getOrder()->getStoreId())) {
            $maxAmount += floatval($this->getCreditmemo()->getSimiuseGiftCreditAmount());
        }
        if ($this->getCreditmemo()->getSimigiftVoucherDiscount()) {
            $maxAmount += floatval($this->getCreditmemo()->getSimigiftVoucherDiscount());
        }
        return Mage::app()->getStore($this->getOrder()->getStoreId())->roundPrice($maxAmount);
    }

    /**
     * @param $price
     * @return mixed
     */
    public function formatPrice($price) {
        return $this->getOrder()->formatPrice($price);
    }

}
