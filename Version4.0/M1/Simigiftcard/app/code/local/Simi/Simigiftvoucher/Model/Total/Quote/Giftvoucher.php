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
 * Class Simi_Simigiftvoucher_Model_Total_Quote_Giftvoucher
 */
class Simi_Simigiftvoucher_Model_Total_Quote_Giftvoucher extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    protected $_hiddentBaseDiscount = 0;
    protected $_hiddentDiscount = 0;

    /**
     * Simi_Simigiftvoucher_Model_Total_Quote_Giftvoucher constructor.
     */
    public function __construct() {
        $this->setCode('simigiftvoucher');
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function collect(Mage_Sales_Model_Quote_Address $address) {
        $quote = $address->getQuote();
        $applyGiftAfterTax = (bool) Mage::helper('simigiftvoucher')->getGeneralConfig('apply_after_tax', $quote->getStoreId());
        if ($applyGiftAfterTax) {
            return $this;
        }
        $session = Mage::getSingleton('checkout/session');
                
        if ($address->getAddressType() == 'billing' && !$quote->isVirtual() || !$session->getSimiuseGiftCard()) {
            return $this;
        }

        if ($codes = $session->getSimigiftCodes()) {
            $codesArray = array_unique(explode(',', $codes));
            $store = $quote->getStore();

            $baseTotalDiscount = 0;
            $totalDiscount = 0;

            $codesBaseDiscount = array();
            $codesDiscount = array();

            $baseSessionAmountUsed = explode(',', $session->getSimibaseAmountUsed());
            $SimibaseAmountUsed = array_combine($codesArray, $baseSessionAmountUsed);
            $amountUsed = $SimibaseAmountUsed;

            $giftMaxUseAmount = unserialize($session->getSimigiftMaxUseAmount());
            if (!is_array($giftMaxUseAmount)) {
                $giftMaxUseAmount = array();
            }
            foreach ($codesArray as $key => $code) {
                $model = Mage::getModel('simigiftvoucher/giftvoucher')->loadByCode($code);
                if ($model->getStatus() != Simi_Simigiftvoucher_Model_Status::STATUS_ACTIVE || $model->getBalance() == 0 || $model->getBaseBalance() <= $SimibaseAmountUsed[$code] || !$model->validate($address)
                ) {
                    $codesBaseDiscount[] = 0;
                    $codesDiscount[] = 0;
                } else {
                    if (Mage::helper('simigiftvoucher')->canUseCode($model)) {
                        $baseBalance = $model->getBaseBalance() - $SimibaseAmountUsed[$code];
                        if (array_key_exists($code, $giftMaxUseAmount)) {
                            $maxDiscount = max(floatval($giftMaxUseAmount[$code]), 0) / $store->convertPrice(1, false, false);
                            $baseBalance = min($baseBalance, $maxDiscount);
                        }
                        if ($baseBalance > 0) {
                            $baseDiscountTotal = 0;
                            foreach ($address->getAllItems() as $item) {
                                if ($item->getParentItemId())
                                    continue;
                                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                                    foreach ($item->getChildren() as $child) {
                                        if (!$child->isDeleted() && $child->getProduct()->getTypeId() != 'simigiftvoucher' && $model->getActions()->validate($child)) {
                                            if (Mage::helper('tax')->priceIncludesTax())
                                                $itemDiscount = $child->getRowTotalInclTax() - $child->getSimibaseDiscount() - $child->getDiscountAmount();
                                            else
                                                $itemDiscount = $child->getBaseRowTotal() - $child->getSimibaseDiscount() - $child->getBaseDiscountAmount();
                                            $baseDiscountTotal += $itemDiscount;
                                        }
                                    }
                                } elseif ($item->getProduct()) {

                                    if (!$item->isDeleted() && $item->getProduct()->getTypeId() != 'simigiftvoucher' && $model->getActions()->validate($item)) {
                                        if (Mage::helper('tax')->priceIncludesTax())
                                            $itemDiscount = $item->getRowTotalInclTax() - $item->getSimibaseDiscount() - $item->getDiscountAmount();
                                        else
                                            $itemDiscount = $item->getBaseRowTotal() - $item->getSimibaseDiscount() - $item->getBaseDiscountAmount();
                                        $baseDiscountTotal += $itemDiscount;
                                    }
                                }
                            }
                            if (Mage::getStoreConfig('simigiftvoucher/general/use_for_ship', $address->getQuote()->getStoreId())) {
                                if (Mage::helper('tax')->shippingPriceIncludesTax())
                                    $shipDiscount = $address->getShippingInclTax() - $address->getSimibaseDiscountForShipping() - $address->getShippingDiscountAmount();
                                else
                                    $shipDiscount = $address->getBaseShippingAmount() - $address->getSimibaseDiscountForShipping() - $address->getBaseShippingDiscountAmount();
                                $baseDiscountTotal += $shipDiscount;
                            }
                        } else
                            $baseDiscount = 0;
                    } else {
                        $baseDiscount = 0;
                    }

                    $baseDiscount = min($baseDiscountTotal, $baseBalance);
                    $discount = $store->convertPrice($baseDiscount);
                    if ($baseDiscountTotal) {
                        $this->prepareGiftDiscountForItem($address, $baseDiscount / $baseDiscountTotal, $store, $model, $baseDiscount);
                    }

                    $SimibaseAmountUsed[$code] += $baseDiscount;
                    $amountUsed[$code] = $store->convertPrice($SimibaseAmountUsed[$code]);

                    $baseTotalDiscount += $baseDiscount;
                    $totalDiscount += $discount;

                    $codesBaseDiscount[] = $baseDiscount;
                    $codesDiscount[] = $discount;
                }
            }
            $codesBaseDiscountString = implode(',', $codesBaseDiscount);
            $codesDiscountString = implode(',', $codesDiscount);

            //update session
            $session->setSimibaseAmountUsed(implode(',', $SimibaseAmountUsed));

            $session->setSimibaseGiftVoucherDiscount($session->getSimibaseGiftVoucherDiscount() + $baseTotalDiscount);
            $session->setSimigiftVoucherDiscount($session->getSimigiftVoucherDiscount() + $totalDiscount);

            $session->setSimicodesBaseDiscount($session->getSimibaseAmountUsed());
            $session->setSimicodesDiscount(implode(',', $amountUsed));

            //update address

            $address->setBaseGrandTotal($address->getBaseGrandTotal() + $this->_hiddentBaseDiscount - $baseTotalDiscount);
            $address->setGrandTotal($address->getGrandTotal() + $this->_hiddentDiscount - $totalDiscount);

            $address->setSimibaseGiftVoucherDiscount($baseTotalDiscount);
            $address->setSimigiftVoucherDiscount($totalDiscount);

            $address->setSimigiftCodes($codes);
            $address->setSimicodesBaseDiscount($codesBaseDiscountString);
            $address->setSimicodesDiscount($codesDiscountString);

            $address->setSimigiftvoucherBaseHiddenTaxAmount($this->_hiddentBaseDiscount);
            $address->setSimigiftvoucherHiddenTaxAmount($this->_hiddentDiscount);

            $address->setSimibaseDiscount($address->getSimibaseDiscount() + $baseTotalDiscount);

            //update quote
            $quote->setSimibaseGiftVoucherDiscount($session->getSimibaseGiftVoucherDiscount());
            $quote->setSimigiftVoucherDiscount($session->getSimigiftVoucherDiscount());

            $quote->setSimigiftCodes($codes);
            $quote->setSimicodesBaseDiscount($session->getSimicodesBaseDiscount());
            $quote->setSimicodesDiscount($session->getSimicodesDiscount());
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
        if ($giftVoucherDiscount = $address->getSimigiftVoucherDiscount()) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => Mage::helper('simigiftvoucher')->__('Gift Card'),
                'value' => -$giftVoucherDiscount,
                'simigift_codes' => $address->getSimigiftCodes(),
                'simicodes_base_discount' => $address->getSimicodesBaseDiscount(),
                'simicodes_discount' => $address->getSimicodesDiscount()
            ));
        }
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @param $rateDiscount
     * @param $store
     * @param $model
     * @param $baseDiscount
     * @return $this
     */
    public function prepareGiftDiscountForItem(Mage_Sales_Model_Quote_Address $address, $rateDiscount, $store, $model, $baseDiscount) {
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {

                foreach ($item->getChildren() as $child) {
                    $discountGiftcardCodes = 0;
                    if (!$child->isDeleted() && $child->getProduct()->getTypeId() != 'simigiftvoucher' && $model->getActions()->validate($child)) {
                        if (Mage::helper('tax')->priceIncludesTax())
                            $itemDiscount = $child->getRowTotalInclTax() - $child->getSimibaseDiscount() - $child->getDiscountAmount();
                        else
                            $itemDiscount = $child->getBaseRowTotal() - $child->getSimibaseDiscount() - $child->getBaseDiscountAmount();
                        $child->setSimibaseDiscount($child->getSimibaseDiscount() + $itemDiscount * $rateDiscount);
                        $child->setSimibaseGiftVoucherDiscount($child->getSimibaseGiftVoucherDiscount() + $itemDiscount * $rateDiscount);
                        $child->setSimigiftVoucherDiscount($child->getSimigiftVoucherDiscount() + $store->convertPrice($itemDiscount * $rateDiscount));

                        $baseTaxableAmount = $child->getBaseTaxableAmount();
                        $taxableAmount = $child->getTaxableAmount();

                        $child->setBaseTaxableAmount($child->getBaseTaxableAmount() - $child->getSimibaseGiftVoucherDiscount());
                        $child->setTaxableAmount($child->getTaxableAmount() - $child->getSimigiftVoucherDiscount());

                        if (Mage::helper('tax')->priceIncludesTax()) {
                            $rate = Mage::helper('simigiftvoucher')->getItemRateOnQuote($child->getProduct(), $store);
                            $hiddenBaseTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($baseTaxableAmount, $rate, true, false);
                            $hiddenTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($taxableAmount, $rate, true, false);

                            $hiddenBaseTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($child->getBaseTaxableAmount(), $rate, true, false);
                            $hiddenTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($child->getTaxableAmount(), $rate, true, false);

                            $hiddentBaseDiscount = Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxAfterDiscount);
                            $hiddentDiscount = Mage::getSingleton('tax/calculation')->round($hiddenTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenTaxAfterDiscount);

                            $child->setSimigiftvoucherBaseHiddenTaxAmount($hiddentBaseDiscount);
                            $child->setSimigiftvoucherHiddenTaxAmount($hiddentDiscount);

                            $this->_hiddentBaseDiscount += $hiddentBaseDiscount;
                            $this->_hiddentDiscount += $hiddentDiscount;
                        }
                    }
                }
            } elseif ($item->getProduct()) {
                if (!$item->isDeleted() && $item->getProduct()->getTypeId() != 'simigiftvoucher' && $model->getActions()->validate($item)) {

                    /* $baseItemPrice = $item->getQty() * $item->getBasePrice() - $item->getBaseDiscountAmount() - $item->getSimibaseDiscount();
                      $itemBaseDiscount = $baseItemPrice * $rateDiscount;
                      $itemDiscount = Mage::app()->getStore()->convertPrice($itemBaseDiscount);

                      $item->setSimibaseGiftVoucherDiscount($item->getSimibaseGiftVoucherDiscount() + $itemBaseDiscount);
                      $item->setSimigiftVoucherDiscount($item->getSimigiftVoucherDiscount() + $itemDiscount);
                      $item->setSimibaseDiscount($item->getSimibaseDiscount() + $itemBaseDiscount); */
                    if (Mage::helper('tax')->priceIncludesTax())
                        $itemDiscount = $item->getRowTotalInclTax() - $item->getSimibaseDiscount() - $item->getDiscountAmount();
                    else
                        $itemDiscount = $item->getBaseRowTotal() - $item->getSimibaseDiscount() - $item->getBaseDiscountAmount();
                    $item->setSimibaseDiscount($item->getSimibaseDiscount() + $itemDiscount * $rateDiscount);
                    $item->setSimibaseGiftVoucherDiscount($item->getSimibaseGiftVoucherDiscount() + $itemDiscount * $rateDiscount);
                    $item->setSimigiftVoucherDiscount($item->getSimigiftVoucherDiscount() + $store->convertPrice($itemDiscount * $rateDiscount));

                    $baseTaxableAmount = $item->getBaseTaxableAmount();
                    $taxableAmount = $item->getTaxableAmount();

                    $item->setBaseTaxableAmount($item->getBaseTaxableAmount() - $item->getSimibaseGiftVoucherDiscount());
                    $item->setTaxableAmount($item->getTaxableAmount() - $item->getSimigiftVoucherDiscount());

                    if (Mage::helper('tax')->priceIncludesTax()) {
                        $rate = Mage::helper('simigiftvoucher')->getItemRateOnQuote($item->getProduct(), $store);
                        $hiddenBaseTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($baseTaxableAmount, $rate, true, false);
                        $hiddenTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($taxableAmount, $rate, true, false);

                        $hiddenBaseTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($item->getBaseTaxableAmount(), $rate, true, false);
                        $hiddenTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($item->getTaxableAmount(), $rate, true, false);

                        $hiddentBaseDiscount = Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxAfterDiscount);
                        $hiddentDiscount = Mage::getSingleton('tax/calculation')->round($hiddenTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenTaxAfterDiscount);

                        $item->setSimigiftvoucherBaseHiddenTaxAmount($hiddentBaseDiscount);
                        $item->setSimigiftvoucherHiddenTaxAmount($hiddentDiscount);

                        $this->_hiddentBaseDiscount += $hiddentBaseDiscount;
                        $this->_hiddentDiscount += $hiddentDiscount;
                    }
                }
                // }
            }
        }
        if (Mage::getStoreConfig('simigiftvoucher/general/use_for_ship', $address->getQuote()->getStoreId())) {
            if (Mage::helper('tax')->shippingPriceIncludesTax())
                $shipDiscount = $address->getShippingInclTax() - $address->getSimibaseDiscountForShipping() - $address->getShippingDiscountAmount();
            else
                $shipDiscount = $address->getBaseShippingAmount() - $address->getSimibaseDiscountForShipping() - $address->getBaseShippingDiscountAmount();

            $address->setSimibaseDiscountForShipping($address->getSimibaseDiscountForShipping() + $shipDiscount * $rateDiscount);
            $address->setSimibaseGiftVoucherDiscountForShipping($address->getSimibaseGiftVoucherDiscountForShipping() + $shipDiscount * $rateDiscount);
            $address->setSimigiftVoucherDiscountForShipping($address->getSimigiftVoucherDiscountForShipping() + $store->convertPrice($shipDiscount * $rateDiscount));

            $baseTaxableAmount = $address->getBaseShippingTaxable();
            $taxableAmount = $address->getShippingTaxable();

            $address->setBaseShippingTaxable($address->getBaseShippingTaxable() - $address->getSimibaseGiftVoucherDiscountForShipping());
            $address->setShippingTaxable($address->getShippingTaxable() - $address->getSimigiftVoucherDiscountForShipping());

            if (Mage::helper('tax')->shippingPriceIncludesTax() && $shipDiscount) {
                $rate = $this->getShipingTaxRate($address, $store);
                $hiddenBaseTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($baseTaxableAmount, $rate, true, false);
                $hiddenTaxBeforeDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($taxableAmount, $rate, true, false);

                $hiddenBaseTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($address->getBaseShippingTaxable(), $rate, true, false);
                $hiddenTaxAfterDiscount = Mage::getSingleton('tax/calculation')->calcTaxAmount($address->getShippingTaxable(), $rate, true, false);

                $hiddentBaseShippingDiscount = Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenBaseTaxAfterDiscount);
                $hiddentShippingDiscount = Mage::getSingleton('tax/calculation')->round($hiddenTaxBeforeDiscount) - Mage::getSingleton('tax/calculation')->round($hiddenTaxAfterDiscount);

                $address->setGiftvoucherBaseShippingHiddenTaxAmount($hiddentBaseShippingDiscount);
                $address->setSimiGiftvoucherShippingHiddenTaxAmount($hiddentShippingDiscount);

                $this->_hiddentBaseDiscount += $hiddentBaseShippingDiscount;
                $this->_hiddentDiscount += $hiddentShippingDiscount;
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
