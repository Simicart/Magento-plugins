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
 * Class Simi_Simigiftvoucher_Model_Total_Quote_Giftcardcreditaftertax
 */
class Simi_Simigiftvoucher_Model_Total_Quote_Giftcardcreditaftertax extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    /**
     * Simi_Simigiftvoucher_Model_Total_Quote_Giftcardcreditaftertax constructor.
     */
    public function __construct() {
        $this->setCode('simigiftcardcredit_after_tax');
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function collect(Mage_Sales_Model_Quote_Address $address) {
        $quote = $address->getQuote();
        $applyGiftAfterTax = (bool) Mage::helper('simigiftvoucher')->getGeneralConfig('apply_after_tax', $quote->getStoreId());
        if (!$applyGiftAfterTax) {
            return $this;
        }
        $session = Mage::getSingleton('checkout/session');
        
        if (!is_object($session)) {
            return $this;
        }

        if (!Mage::helper('simigiftvoucher')->getGeneralConfig('enablecredit', $quote->getStoreId())) {
            $session->setSimibaseUseGiftCreditAmount(0);
            $session->setUseGiftCreditAmount(0);
            return $this;
        }
        if (Mage::app()->getStore()->isAdmin()) {
            $customer = Mage::getSingleton('adminhtml/session_quote')->getCustomer();
        } else {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
        }

        if ($address->getAddressType() == 'billing' && !$quote->isVirtual() || !$session->getUseGiftCardCredit() || !$customer->getId()
        ) {

            return $this;
        }
        $credit = Mage::getModel('simigiftvoucher/credit')->load(
                $customer->getId(), 'customer_id'
        );
        if ($credit->getBalance() < 0.0001) {
            $session->setSimibaseUseGiftCreditAmount(0);
            $session->setUseGiftCreditAmount(0);
            return $this;
        }
        $store = $quote->getStore();
        $baseBalance = 0;
        if ($rate = $store->getBaseCurrency()->getRate($credit->getData('currency'))) {
            $baseBalance = $credit->getBalance() / $rate;
        }
        if ($baseBalance < 0.0001) {
            $session->setSimibaseUseGiftCreditAmount(0);
            $session->setUseGiftCreditAmount(0);
            return $this;
        }

        if ($session->getMaxCreditUsed() > 0.0001) {
            $baseBalance = min($baseBalance, floatval($session->getMaxCreditUsed()) / $store->convertPrice(1, false, false));
        }

        $baseTotalDiscount = 0;
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    if (!$child->isDeleted() && $child->getProduct()->getTypeId() != 'simigiftvoucher') {
                        $itemDiscount = $child->getBaseRowTotal() - $child->getSimibaseDiscount() - $child->getBaseDiscountAmount() + $child->getBaseTaxAmount();
                        $baseTotalDiscount += $itemDiscount;
                    }
                }
            } elseif ($item->getProduct()) {
                if (!$item->isDeleted() && $item->getProduct()->getTypeId() != 'simigiftvoucher') {
                    $itemDiscount = $item->getBaseRowTotal() - $item->getSimibaseDiscount() - $item->getBaseDiscountAmount() + $item->getBaseTaxAmount();
                    $baseTotalDiscount += $itemDiscount;
                }
            }
        }
        if (Mage::getStoreConfig('simigiftvoucher/general/use_for_ship', $address->getQuote()->getStoreId())) {
            $shipDiscount = $address->getBaseShippingAmount() - $address->getSimibaseDiscountForShipping() - $address->getBaseShippingDiscountAmount() + $address->getBaseShippingTaxAmount();
            $baseTotalDiscount += $shipDiscount;
        }
        $baseDiscount = min($baseTotalDiscount, $baseBalance);
        $discount = $store->convertPrice($baseDiscount);
        if ($baseTotalDiscount != 0)
            $this->prepareGiftDiscountForItem($address, $baseDiscount / $baseTotalDiscount, $store, $baseDiscount);

        if ($baseDiscount && $discount) {
            $session->setSimibaseUseGiftCreditAmount($baseDiscount);
            $session->setUseGiftCreditAmount($discount);

            $address->setSimigiftcardCreditAmount($baseDiscount * $rate);
            $address->setSimibaseUseGiftCreditAmount($baseDiscount);
            $address->setSimiuseGiftCreditAmount($discount);

            $address->setSimibaseDiscount($address->getSimibaseDiscount() + $baseDiscount);

            $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseDiscount);
            $address->setGrandTotal($store->convertPrice($address->getBaseGrandTotal()));
        }

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address) {
        $quote = $address->getQuote();
        $applyGiftAfterTax = (bool) Mage::helper('simigiftvoucher')->getGeneralConfig('apply_after_tax', $quote->getStoreId());
        if (!$applyGiftAfterTax) {
            return $this;
        }
        $amount = $address->getSimiuseGiftCreditAmount();
        if ($amount > 0) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => Mage::helper('simigiftvoucher')->__('Gift Card credit'),
                'value' => -$amount
            ));
        }
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @param $rateDiscount
     * @param $store
     * @param $baseDiscount
     * @return $this
     */
    public function prepareGiftDiscountForItem(Mage_Sales_Model_Quote_Address $address, $rateDiscount, $store, $baseDiscount) {
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $discountGiftcardCredit = 0;
                    if (!$child->isDeleted() && $child->getProduct()->getTypeId() != 'simigiftvoucher') {
                        $itemDiscount = $child->getBaseRowTotal() - $child->getSimibaseDiscount() - $child->getBaseDiscountAmount() + $child->getBaseTaxAmount();
                        $child->setSimibaseDiscount($child->getSimibaseDiscount() + $itemDiscount * $rateDiscount);
                        $child->setSimibaseUseGiftCreditAmount($child->getSimibaseUseGiftCreditAmount() + $itemDiscount * $rateDiscount);
                        $child->setSimiuseGiftCreditAmount($child->getSimiuseGiftCreditAmount() + $store->convertPrice($itemDiscount * $rateDiscount));
                    }
                }
            } elseif ($item->getProduct()) {
                if (!$item->isDeleted() && $item->getProduct()->getTypeId() != 'simigiftvoucher') {
                    $itemDiscount = $item->getBaseRowTotal() - $item->getSimibaseDiscount() - $item->getBaseDiscountAmount() + $item->getBaseTaxAmount();
                    $item->setSimibaseDiscount($item->getSimibaseDiscount() + $itemDiscount * $rateDiscount);
                    $item->setSimibaseUseGiftCreditAmount($item->getSimibaseUseGiftCreditAmount() + $itemDiscount * $rateDiscount);
                    $item->setSimiuseGiftCreditAmount($item->getSimiuseGiftCreditAmount() + $store->convertPrice($itemDiscount * $rateDiscount));
                }
            }
        }
        if (Mage::getStoreConfig('simigiftvoucher/general/use_for_ship', $address->getQuote()->getStoreId())) {
            $shipDiscount = $address->getBaseShippingAmount() - $address->getSimibaseDiscountForShipping() - $address->getBaseShippingDiscountAmount() + $address->getBaseShippingTaxAmount();
            $address->setSimibaseDiscountForShipping($address->getSimibaseDiscountForShipping() + $shipDiscount * $rateDiscount);
            $address->setSimiBaseGiftcreditDiscountForShipping($address->getSimibaseGiftVoucherDiscountForShipping() + $shipDiscount * $rateDiscount);
            $address->setSimiGiftcreditDiscountForShipping($address->getSimigiftVoucherDiscountForShipping() + $store->convertPrice($shipDiscount * $rateDiscount));
        }
        return $this;
    }

    /**
     * @param $session
     */
    public function clearGiftcardSession($session) {
        if ($session->getUseGiftCard())
            $session->setUseGiftCard(null)
                    ->setGiftCodes(null)
                    ->setBaseAmountUsed(null)
                    ->setSimibaseGiftVoucherDiscount(null)
                    ->setSimigiftVoucherDiscount(null)
                    ->setCodesBaseDiscount(null)
                    ->setCodesDiscount(null)
                    ->setGiftMaxUseAmount(null);
        if ($session->getUseGiftCardCredit()) {
            $session->setUseGiftCardCredit(null)
                    ->setMaxCreditUsed(null)
                    ->setSimibaseUseGiftCreditAmount(null)
                    ->setSimiuseGiftCreditAmount(null);
        }
    }

}
