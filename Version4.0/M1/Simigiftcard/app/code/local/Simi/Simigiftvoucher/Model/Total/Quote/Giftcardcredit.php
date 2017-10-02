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
 * Class Simi_Simigiftvoucher_Model_Total_Quote_Giftcardcredit
 */
class Simi_Simigiftvoucher_Model_Total_Quote_Giftcardcredit extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    protected $_hiddentBaseDiscount = 0;
    protected $_hiddentDiscount = 0;

    /**
     * Simi_Simigiftvoucher_Model_Total_Quote_Giftcardcredit constructor.
     */
    public function __construct() {
        $this->setCode('simigiftcardcredit');
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function collect(Mage_Sales_Model_Quote_Address $address) {
        $quote = $address->getQuote();
        $applyGiftAfterTax = (bool) Mage::helper('simigiftvoucher')->getGeneralConfig('apply_after_tax', $quote->getStoreId());
        if ($applyGiftAfterTax) {
            return $this;
        }
        $session = Mage::getSingleton('checkout/session');
        
        if (!is_object($session)) {
            return $this;
        }


        if (!Mage::helper('simigiftvoucher')->getGeneralConfig('enablecredit', $quote->getStoreId())) {
            $session->setSimibaseUseGiftCreditAmount(0);
            $session->setSimiuseGiftCreditAmount(0);
            return $this;
        }
        if (Mage::app()->getStore()->isAdmin()) {
            $customer = Mage::getSingleton('adminhtml/session_quote')->getCustomer();
        } else {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
        }

        if ($address->getAddressType() == 'billing' && !$quote->isVirtual() || !$session->getSimiuseGiftCardCredit() || !$customer->getId()
        ) {
//            $session->setSimibaseUseGiftCreditAmount(0);
//            $session->setSimiuseGiftCreditAmount(0);
            return $this;
        }
        $credit = Mage::getModel('simigiftvoucher/credit')->load(
                $customer->getId(), 'customer_id'
        );
        if ($credit->getBalance() < 0.0001) {
            $session->setSimibaseUseGiftCreditAmount(0);
            $session->setSimiuseGiftCreditAmount(0);
            return $this;
        }
        $store = $quote->getStore();
        $baseBalance = 0;
        if ($rateCredit = $store->getBaseCurrency()->getRate($credit->getData('currency'))) {
            $baseBalance = $credit->getBalance() / $rateCredit;
        }
        if ($baseBalance < 0.0001) {
            $session->setSimibaseUseGiftCreditAmount(0);
            $session->setSimiuseGiftCreditAmount(0);
            return $this;
        }

        if ($session->getSimimaxCreditUsed() > 0.0001) {
            $baseBalance = min($baseBalance, floatval($session->getSimimaxCreditUsed()));
        }

        $baseTotalDiscount = 0;
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    if (!$child->isDeleted() && $child->getProduct()->getTypeId() != 'simigiftvoucher') {
                        if (Mage::helper('tax')->priceIncludesTax())
                            $itemDiscount = $child->getRowTotalInclTax() - $child->getSimibaseDiscount() - $child->getDiscountAmount();
                        else
                            $itemDiscount = $child->getBaseRowTotal() - $child->getSimibaseDiscount() - $child->getBaseDiscountAmount();
                        $baseTotalDiscount += $itemDiscount;
                    }
                }
            } elseif ($item->getProduct()) {
                if (!$item->isDeleted() && $item->getProduct()->getTypeId() != 'simigiftvoucher') {
                    if (Mage::helper('tax')->priceIncludesTax())
                        $itemDiscount = $item->getRowTotalInclTax() - $item->getSimibaseDiscount() - $item->getDiscountAmount();
                    else
                        $itemDiscount = $item->getBaseRowTotal() - $item->getSimibaseDiscount() - $item->getBaseDiscountAmount();
                    $baseTotalDiscount += $itemDiscount;
                }
            }
        }
        if (Mage::getStoreConfig('simigiftvoucher/general/use_for_ship', $address->getQuote()->getStoreId())) {
            if (Mage::helper('tax')->shippingPriceIncludesTax())
                $shipDiscount = $address->getShippingInclTax() - $address->getSimibaseDiscountForShipping() - $address->getShippingDiscountAmount();
            else
                $shipDiscount = $address->getBaseShippingAmount() - $address->getSimibaseDiscountForShipping() - $address->getBaseShippingDiscountAmount();
            $baseTotalDiscount += $shipDiscount;
        }

        $baseDiscount = min($baseTotalDiscount, $baseBalance);
        $discount = $store->convertPrice($baseDiscount);
        $this->prepareGiftDiscountForItem($address, $baseDiscount / $baseTotalDiscount, $store, $baseDiscount);

        if ($baseDiscount && $discount) {
            $session->setSimibaseUseGiftCreditAmount($baseDiscount);
            $session->setSimiUseGiftCreditAmount($discount);

            $address->setSimigiftcardCreditAmount($baseDiscount * $rateCredit);
            $address->setSimibaseUseGiftCreditAmount($baseDiscount);
            $address->setSimiuseGiftCreditAmount($discount);

            $address->setSimibaseDiscount($address->getSimibaseDiscount() + $baseDiscount);

            $address->setBaseGrandTotal($address->getBaseGrandTotal() + $this->_hiddentBaseDiscount - $baseDiscount);
            $address->setGrandTotal($address->getGrandTotal() + $this->_hiddentDiscount - $discount);
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
        if ($applyGiftAfterTax) {
            return $this;
        }
        if ($amount = $address->getSimiuseGiftCreditAmount()) {
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
                        if (Mage::helper('tax')->priceIncludesTax())
                            $itemDiscount = $child->getRowTotalInclTax() - $child->getSimibaseDiscount() - $child->getDiscountAmount();
                        else
                            $itemDiscount = $child->getBaseRowTotal() - $child->getSimibaseDiscount() - $child->getBaseDiscountAmount();
                        $child->setSimibaseDiscount($child->getSimibaseDiscount() + $itemDiscount * $rateDiscount);
                        $child->setSimibaseUseGiftCreditAmount($child->getSimibaseUseGiftCreditAmount() + $itemDiscount * $rateDiscount);
                        $child->setSimiuseGiftCreditAmount($child->getSimiuseGiftCreditAmount() + $store->convertPrice($itemDiscount * $rateDiscount));
                        $baseTaxableAmount = $child->getBaseTaxableAmount();
                        $taxableAmount = $child->getTaxableAmount();

                        $child->setBaseTaxableAmount($child->getBaseTaxableAmount() - $child->getSimibaseUseGiftCreditAmount());
                        $child->setTaxableAmount($child->getTaxableAmount() - $child->getSimiuseGiftCreditAmount());

                        if (Mage::helper('tax')->priceIncludesTax()) {
                            $rate = Mage::helper('simigiftvoucher')->getItemRateOnQuote($item->getProduct(), $store);
                            $hiddenBaseTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($baseTaxableAmount, $rate, true, false);
                            $hiddenTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($taxableAmount, $rate, true, false);

                            $hiddenBaseTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($child->getBaseTaxableAmount(), $rate, true, false);
                            $hiddenTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($child->getTaxableAmount(), $rate, true, false);


                            $hiddentBaseDiscount = Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxAfterDiscount);
                            $hiddentDiscount = Mage::getSingleton('tax/calculation')->round($hiddenTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenTaxAfterDiscount);

                            $this->_hiddentBaseDiscount += $hiddentBaseDiscount;
                            $this->_hiddentDiscount += $hiddentDiscount;
                        }
                    }
                }
            } elseif ($item->getProduct()) {
                if (!$item->isDeleted() && $item->getProduct()->getTypeId() != 'simigiftvoucher') {
                    if (Mage::helper('tax')->priceIncludesTax())
                        $itemDiscount = $item->getRowTotalInclTax() - $item->getSimibaseDiscount() - $item->getDiscountAmount();
                    else
                        $itemDiscount = $item->getBaseRowTotal() - $item->getSimibaseDiscount() - $item->getBaseDiscountAmount();
                    $item->setSimibaseDiscount($item->getSimibaseDiscount() + $itemDiscount * $rateDiscount);
                    $item->setSimibaseUseGiftCreditAmount($item->getSimibaseUseGiftCreditAmount() + $itemDiscount * $rateDiscount);
                    $item->setSimiuseGiftCreditAmount($item->getSimiuseGiftCreditAmount() + $store->convertPrice($itemDiscount * $rateDiscount));

                    $baseTaxableAmount = $item->getBaseTaxableAmount();
                    $taxableAmount = $item->getTaxableAmount();

                    $item->setBaseTaxableAmount($item->getBaseTaxableAmount() - $item->getSimibaseUseGiftCreditAmount());
                    $item->setTaxableAmount($item->getTaxableAmount() - $item->getSimiuseGiftCreditAmount());

                    if (Mage::helper('tax')->priceIncludesTax()) {
                        $rate = Mage::helper('simigiftvoucher')->getItemRateOnQuote($item->getProduct(), $store);
                        $hiddenBaseTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($baseTaxableAmount, $rate, true, false);
                        $hiddenTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($taxableAmount, $rate, true, false);

                        $hiddenBaseTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($item->getBaseTaxableAmount(), $rate, true, false);
                        $hiddenTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($item->getTaxableAmount(), $rate, true, false);


                        $hiddentBaseDiscount = Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxAfterDiscount);
                        $hiddentDiscount = Mage::getSingleton('tax/calculation')->round($hiddenTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenTaxAfterDiscount);

                        $this->_hiddentBaseDiscount += $hiddentBaseDiscount;
                        $this->_hiddentDiscount += $hiddentDiscount;
                    }
                }
            }
        }
        if (Mage::getStoreConfig('simigiftvoucher/general/use_for_ship', $address->getQuote()->getStoreId())) {
            if (Mage::helper('tax')->shippingPriceIncludesTax())
                $shipDiscount = $address->getShippingInclTax() - $address->getSimibaseDiscountForShipping() - $address->getShippingDiscountAmount();
            else
                $shipDiscount = $address->getBaseShippingAmount() - $address->getSimibaseDiscountForShipping() - $address->getBaseShippingDiscountAmount();
            $address->setSimibaseDiscountForShipping($address->getSimibaseDiscountForShipping() + $shipDiscount * $rateDiscount);
            $address->setSimiBaseGiftcreditDiscountForShipping($address->getSimiBaseGiftcreditDiscountForShipping() + $shipDiscount * $rateDiscount);
            $address->setSimiGiftcreditDiscountForShipping($address->getSimiGiftcreditDiscountForShipping() + $store->convertPrice($shipDiscount * $rateDiscount));

            $baseTaxableAmount = $address->getBaseShippingTaxable();
            $taxableAmount = $address->getShippingTaxable();

            $address->setBaseShippingTaxable($address->getBaseShippingTaxable() - $address->getSimiBaseGiftcreditDiscountForShipping());
            $address->setShippingTaxable($address->getShippingTaxable() - $address->getSimiGiftcreditDiscountForShipping());

            if (Mage::helper('tax')->shippingPriceIncludesTax() && $shipDiscount) {
                $rate = $this->getShipingTaxRate($address, $store);
                $hiddenBaseTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($baseTaxableAmount, $rate, true, false);
                $hiddenTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($taxableAmount, $rate, true, false);

                $hiddenBaseTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($address->getBaseShippingTaxable(), $rate, true, false);
                $hiddenTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($address->getShippingTaxable(), $rate, true, false);

                $this->_hiddentBaseDiscount += Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxAfterDiscount);
                $this->_hiddentDiscount += Mage::getSingleton('tax/calculation')->round($hiddenTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenTaxAfterDiscount);
            }
        }
        return $this;
    }

    /**
     * @param $address
     * @param $store
     * @return mixed
     */
    public function getShipingTaxRate($address, $store) {
        $request = Mage::getSingleton('tax/calculation')->getRateRequest(
                $address, $address->getQuote()->getBillingAddress(), $address->getQuote()->getCustomerTaxClassId(), $store
        );
        $request->setProductClassId(Mage::getSingleton('tax/config')->getShippingTaxClass($store));
        $rate = Mage::getSingleton('tax/calculation')->getRate($request);
        return $rate;
    }

    /**
     * @param $session
     */
    public function clearGiftcardSession($session) {
        if ($session->getSimiuseGiftCard())
            $session->setSimiuseGiftCard(null)
                    ->setSimigiftCodes(null)
                    ->setSimibaseAmountUsed(null)
                    ->setSimibaseGiftVoucherDiscount(null)
                    ->setSimigiftVoucherDiscount(null)
                    ->setSimicodesBaseDiscount(null)
                    ->setSimicodesDiscount(null)
                    ->setSimigiftMaxUseAmount(null);
        if ($session->getSimiuseGiftCardCredit()) {
            $session->setSimiuseGiftCardCredit(null)
                    ->setSimimaxCreditUsed(null)
                    ->setSimibaseUseGiftCreditAmount(null)
                    ->setSimiuseGiftCreditAmount(null);
        }
    }

}
