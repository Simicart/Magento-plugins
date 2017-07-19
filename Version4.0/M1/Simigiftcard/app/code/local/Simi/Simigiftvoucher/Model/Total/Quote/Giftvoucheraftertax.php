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
 * Class Simi_Simigiftvoucher_Model_Total_Quote_Giftvoucheraftertax
 */
class Simi_Simigiftvoucher_Model_Total_Quote_Giftvoucheraftertax extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    /**
     * Simi_Simigiftvoucher_Model_Total_Quote_Giftvoucheraftertax constructor.
     */
    public function __construct() {
        $this->setCode('simigiftvoucher_after_tax');
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function collect(Mage_Sales_Model_Quote_Address $address) {
        $quote = $address->getQuote();
        $applyGiftAfterTax = (bool) Mage::helper('simigiftvoucher')->getGeneralConfig('apply_after_tax', $quote->getStoreId());
        if (!$applyGiftAfterTax) {
            return $this;
        }
        $session = Mage::getSingleton('checkout/session');

        if ($address->getAddressType() == 'billing' && !$quote->isVirtual() || !$session->getUseGiftCard()) {
            return $this;
        }

        if ($codes = $session->getGiftCodes()) {
            $codesArray = array_unique(explode(',', $codes));
            $store = $quote->getStore();

            $baseTotalDiscount = 0;
            $totalDiscount = 0;

            $codesBaseDiscount = array();
            $codesDiscount = array();

            $baseSessionAmountUsed = explode(',', $session->getBaseAmountUsed());
            $baseAmountUsed = array_combine($codesArray, $baseSessionAmountUsed);
            $amountUsed = $baseAmountUsed;

            $giftMaxUseAmount = unserialize($session->getGiftMaxUseAmount());
            if (!is_array($giftMaxUseAmount)) {
                $giftMaxUseAmount = array();
            }
            foreach ($codesArray as $key => $code) {
                $model = Mage::getModel('simigiftvoucher/giftvoucher')->loadByCode($code);
                if ($model->getStatus() != Simi_Simigiftvoucher_Model_Status::STATUS_ACTIVE || $model->getBalance() == 0 || $model->getBaseBalance() <= $baseAmountUsed[$code] || !$model->validate($address)
                ) {
                    $codesBaseDiscount[] = 0;
                    $codesDiscount[] = 0;
                } else {
                    if (Mage::helper('simigiftvoucher')->canUseCode($model)) {
                        $baseBalance = $model->getBaseBalance() - $baseAmountUsed[$code];
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
                                            $itemDiscount = $child->getBaseRowTotal() - $child->getSimibaseDiscount() - $child->getBaseDiscountAmount() + $child->getBaseTaxAmount();
                                            $baseDiscountTotal += $itemDiscount;
                                        }
                                    }
                                } elseif ($item->getProduct()) {
                                    if (!$item->isDeleted() && $item->getProduct()->getTypeId() != 'simigiftvoucher' && $model->getActions()->validate($item)) {
                                        $itemDiscount = $item->getBaseRowTotal() - $item->getSimibaseDiscount() - $item->getBaseDiscountAmount() + $item->getBaseTaxAmount();
                                        $baseDiscountTotal += $itemDiscount;
                                    }
                                }
                            }
                            if (Mage::getStoreConfig('simigiftvoucher/general/use_for_ship', $address->getQuote()->getStoreId())) {
                                $shipDiscount = $address->getBaseShippingAmount() - $address->getSimibaseDiscountForShipping() - $address->getBaseShippingDiscountAmount() + $address->getBaseShippingTaxAmount();
                                $baseDiscountTotal += $shipDiscount;
                            }
                        } else
                            $baseDiscount = 0;
                    } else {
                        $baseDiscount = 0;
                    }
                    $baseDiscount = min($baseDiscountTotal, $baseBalance);
                    $discount = $store->convertPrice($baseDiscount);
                    if ($baseDiscountTotal != 0)
                        $this->prepareGiftDiscountForItem($address, $baseDiscount / $baseDiscountTotal, $store, $model, $baseDiscount);

                    $baseAmountUsed[$code] += $baseDiscount;
                    $amountUsed[$code] = $store->convertPrice($baseAmountUsed[$code]);

                    $baseTotalDiscount += $baseDiscount;
                    $totalDiscount += $discount;

                    $codesBaseDiscount[] = $baseDiscount;
                    $codesDiscount[] = $discount;
                }
            }
            $codesBaseDiscountString = implode(',', $codesBaseDiscount);
            $codesDiscountString = implode(',', $codesDiscount);

            //update session
            $session->setBaseAmountUsed(implode(',', $baseAmountUsed));

            $session->setSimibaseGiftVoucherDiscount($session->getSimibaseGiftVoucherDiscount() + $baseTotalDiscount);
            $session->setSimigiftVoucherDiscount($session->getSimigiftVoucherDiscount() + $totalDiscount);

            $session->setCodesBaseDiscount($session->getBaseAmountUsed());
            $session->setCodesDiscount(implode(',', $amountUsed));

            //update address
            $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseTotalDiscount);
            $address->setGrandTotal($store->convertPrice($address->getBaseGrandTotal()));

            $address->setSimibaseGiftVoucherDiscount($baseTotalDiscount);
            $address->setSimigiftVoucherDiscount($totalDiscount);

            $address->setSimigiftCodes($codes);
            $address->setSimicodesBaseDiscount($codesBaseDiscountString);
            $address->setSimicodesDiscount($codesDiscountString);

            $address->setSimibaseDiscount($address->getSimibaseDiscount() + $baseTotalDiscount);

            //update quote
            $quote->setSimibaseGiftVoucherDiscount($session->getSimibaseGiftVoucherDiscount());
            $quote->setSimigiftVoucherDiscount($session->getSimigiftVoucherDiscount());

            $quote->setSimigiftCodes($codes);
            $quote->setSimicodesBaseDiscount($session->getCodesBaseDiscount());
            $quote->setSimicodesDiscount($session->getCodesDiscount());
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
        $giftVoucherDiscount = $address->getSimigiftVoucherDiscount();
        if ($giftVoucherDiscount > 0) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => Mage::helper('simigiftvoucher')->__('Simi Gift Card'),
                'value' => -$giftVoucherDiscount,
                'simigift_codes' => $address->getSimigiftCodes(),
                'simicodes_base_discount' => $address->getSimicodesBaseDiscount(),
                'simicodes_discount' => $address->getSimicodesDiscount()
            ));
        }
        //zend_debug::dump($address->debug());
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
                        $itemDiscount = $child->getBaseRowTotal() - $child->getSimibaseDiscount() - $child->getBaseDiscountAmount() + $child->getBaseTaxAmount();
                        $child->setSimibaseDiscount($child->getSimibaseDiscount() + $itemDiscount * $rateDiscount);
                        $child->setSimibaseGiftVoucherDiscount($child->getSimibaseGiftVoucherDiscount() + $itemDiscount * $rateDiscount);
                        $child->setSimigiftVoucherDiscount($child->getSimigiftVoucherDiscount() + $store->convertPrice($itemDiscount * $rateDiscount));
                    }
                }
            } elseif ($item->getProduct()) {
                if (!$item->isDeleted() && $item->getProduct()->getTypeId() != 'simigiftvoucher' && $model->getActions()->validate($item)) {
                    $itemDiscount = $item->getBaseRowTotal() - $item->getSimibaseDiscount() - $item->getBaseDiscountAmount() + $item->getBaseTaxAmount();
                    $item->setSimibaseDiscount($item->getSimibaseDiscount() + $itemDiscount * $rateDiscount);
                    $item->setSimibaseGiftVoucherDiscount($item->getSimibaseGiftVoucherDiscount() + $itemDiscount * $rateDiscount);
                    $item->setSimigiftVoucherDiscount($item->getSimigiftVoucherDiscount() + $store->convertPrice($itemDiscount * $rateDiscount));
                }
            }
        }
        if (Mage::getStoreConfig('simigiftvoucher/general/use_for_ship', $address->getQuote()->getStoreId())) {
            $shipDiscount = $address->getBaseShippingAmount() - $address->getSimibaseDiscountForShipping() - $address->getBaseShippingDiscountAmount() + $address->getBaseShippingTaxAmount();
            $address->setSimibaseDiscountForShipping($address->getSimibaseDiscountForShipping() + $shipDiscount * $rateDiscount);
            $address->setSimibaseGiftVoucherDiscountForShipping($address->getSimibaseGiftVoucherDiscountForShipping() + $shipDiscount * $rateDiscount);
            $address->setSimigiftVoucherDiscountForShipping($address->getSimigiftVoucherDiscountForShipping() + $store->convertPrice($shipDiscount * $rateDiscount));
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
