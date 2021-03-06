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
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Order_Form
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Order_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract {

    /**
     * @return array
     */
    public function getGiftVoucherDiscount() {
        $session = Mage::getSingleton('checkout/session');
        $discounts = array();
        if ($codes = $session->getSimigiftCodes()) {
            $codesArray = explode(',', $codes);
            $codesDiscountArray = explode(',', $session->getSimicodesDiscount());
            $discounts = array_combine($codesArray, $codesDiscountArray);
        }
        return $discounts;
    }

    /**
     * @return string
     */
    public function getAddGiftVoucherUrl() {
        return trim($this->getUrl('simigiftvoucher/adminhtml_checkout/addgift'), '/');
    }

    /**
     * check customer use gift card to checkout
     * 
     * @return boolean
     */
    public function getUseGiftVoucher() {
        return Mage::getSingleton('checkout/session')->getSimiuseGiftCard();
    }

    /**
     * @return int
     */
    public function checkCustomerIsLoggedIn() {
        return $this->getCustomerId();
    }

    /**
     * get existed gift Card
     * 
     * @return array
     */
    public function getExistedGiftCard() {
        $customerId = $this->getCustomerId();
        $collection = Mage::getResourceModel('simigiftvoucher/customervoucher_collection')
                ->addFieldToFilter('main_table.customer_id', $customerId);
        $voucherTable = $collection->getTable('simigiftvoucher/giftvoucher');
        $collection->getSelect()
                ->join(array('v' => $voucherTable), 'main_table.voucher_id = v.giftvoucher_id', array('gift_code', 'balance', 'currency', 'conditions_serialized')
                )->where('v.status = ?', Simi_Simigiftvoucher_Model_Status::STATUS_ACTIVE)
                ->where("v.recipient_name IS NULL OR v.recipient_name = '' OR (v.customer_id <> '" .
                        $customerId . "' AND v.customer_email <> ?)", $this->getCustomer()->getEmail())
                ->where("v.set_id IS NULL OR v.set_id <= 0 ")
        ;
        // ->where("v.recipient_name IS NULL OR v.recipient_name = ''")
        // ->where("v.recipient_email IS NULL OR v.recipient_email = ''");
        $giftCards = array();
        $addedCodes = array();
        if ($codes = Mage::getSingleton('checkout/session')->getSimigiftCodes()) {
            $addedCodes = explode(',', $codes);
        }
        $conditions = Mage::getSingleton('simigiftvoucher/giftvoucher')->getConditions();
        $quote = $this->getQuote();
        $quote->setQuote($quote);
        foreach ($collection as $item) {
            if (in_array($item->getGiftCode(), $addedCodes)) {
                continue;
            }
            if ($item->getConditionsSerialized()) {
                $conditionsArr = unserialize($item->getConditionsSerialized());
                if (!empty($conditionsArr) && is_array($conditionsArr)) {
                    $conditions->setConditions(array())->loadArray($conditionsArr);
                    if (!$conditions->validate($quote)) {
                        continue;
                    }
                }
            }
            $giftCards[] = array(
                'gift_code' => $item->getGiftCode(),
                // 'hidden_code'   => $helper->getHiddenCode($item->getGiftCode()),
                'balance' => $this->getGiftCardBalance($item)
            );
        }
        return $giftCards;
    }

    /**
     * @param $item
     * @return mixed
     */
    public function getGiftCardBalance($item) {
        $cardCurrency = Mage::getModel('directory/currency')->load($item->getCurrency());
        /* @var Mage_Core_Model_Store */
        $store = Mage::getSingleton('adminhtml/session_quote')->getStore();
        $baseCurrency = $store->getBaseCurrency();
        $currentCurrency = $store->getCurrentCurrency();
        if ($cardCurrency->getCode() == $currentCurrency->getCode()) {
            return $store->formatPrice($item->getBalance());
        }
        if ($cardCurrency->getCode() == $baseCurrency->getCode()) {
            return $store->convertPrice($item->getBalance(), true);
        }
        if ($baseCurrency->convert(100, $cardCurrency)) {
            $amount = $item->getBalance() * $baseCurrency->convert(100, $currentCurrency) / $baseCurrency->convert(100, $cardCurrency);
            return $store->formatPrice($amount);
        }
        return $cardCurrency->format($item->getBalance(), array(), true);
    }

    /**
     * get customer Credit to checkout
     * 
     * @return Simi_Simigiftvoucher_Model_Credit
     */
    public function getCustomerCredit() {
        if ($this->checkCustomerIsLoggedIn()) {
            $credit = Mage::getModel('simigiftvoucher/credit')->load(
                    $this->getCustomerId(), 'customer_id'
            );
            if ($credit->getBalance() > 0.0001) {
                return $credit;
            }
        }
        return false;
    }

    /**
     * @param $credit
     * @param bool $showUpdate
     * @return mixed
     */
    public function formatBalance($credit, $showUpdate = false) {
        if ($showUpdate) {
            $cardCurrency = Mage::getModel('directory/currency')->load($credit->getCurrency());
            /* @var Mage_Core_Model_Store */
            $store = Mage::getSingleton('adminhtml/session_quote')->getStore();
            $baseCurrency = $store->getBaseCurrency();
            $currentCurrency = $store->getCurrentCurrency();
            if ($cardCurrency->getCode() == $currentCurrency->getCode()) {
                return $store->formatPrice($credit->getBalance() - $this->getSimiuseGiftCreditAmount());
            }
            if ($cardCurrency->getCode() == $baseCurrency->getCode()) {
                $amount = $store->convertPrice($credit->getBalance(), false);
                return $store->formatPrice($amount - $this->getSimiuseGiftCreditAmount());
            }
            if ($baseCurrency->convert(100, $cardCurrency)) {
                $amount = $credit->getBalance() * $baseCurrency->convert(100, $currentCurrency) / $baseCurrency->convert(100, $cardCurrency);
                return $store->formatPrice($amount - $this->getSimiuseGiftCreditAmount());
            }
            return $cardCurrency->format($credit->getBalance(), array(), true);
        }
        return $this->getGiftCardBalance($credit);
    }

    /**
     * check customer use gift credit to checkout
     * 
     * @return boolean
     */
    public function getUseGiftCredit() {
        return Mage::getSingleton('checkout/session')->getSimiuseGiftCardCredit();
    }

    /**
     * @return string
     */
    public function getUsingAmount() {
        return $this->getStore()->formatPrice(
                        Mage::getSingleton('checkout/session')->getSimiuseGiftCreditAmount()
        );
    }

    /**
     * @return mixed
     */
    public function getSimiuseGiftCreditAmount() {
        return Mage::getSingleton('checkout/session')->getSimiuseGiftCreditAmount();
    }

}
