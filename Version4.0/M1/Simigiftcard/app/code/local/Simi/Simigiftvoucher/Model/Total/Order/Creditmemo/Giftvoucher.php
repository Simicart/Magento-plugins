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
 * Giftvoucher Total Order Creditmemo Giftvoucher Model
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */

class Simi_Simigiftvoucher_Model_Total_Order_Creditmemo_Giftvoucher 
    extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{

    /**
     * Collect creditmemo simigiftvoucher
     *
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return Simi_Simigiftvoucher_Model_Total_Order_Creditmemo_Giftvoucher
     */
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        if ($order->getPayment()->getMethod() == 'simigiftvoucher') {
            return $this;
        }
        if (!$order->getSimigiftVoucherDiscount() && !$order->getSimiuseGiftCreditAmount()) {
            return $this;
        }

        $creditmemo->setSimiuseGiftCreditAmount(0);
        $creditmemo->setSimibaseUseGiftCreditAmount(0);
        $creditmemo->setSimibaseGiftVoucherDiscount(0);
        $creditmemo->setSimigiftVoucherDiscount(0);

        $totalDiscountAmountGiftvoucher = 0;
        $baseTotalDiscountAmountGiftvoucher = 0;
        $totalDiscountAmountCredit = 0;
        $baseTotalDiscountAmountCredit = 0;

        $totalGiftvoucherDiscountRefunded = 0;
        $baseGiftvoucherTotalDiscountRefunded = 0;
        $totalGiftcreditDiscountRefunded = 0;
        $baseGiftcreditTotalDiscountRefunded = 0;

        $hiddenGiftvoucherTaxRefunded = 0;
        $baseGiftvoucherHiddenTaxRefunded = 0;
        $hiddenGiftcreditTaxRefunded = 0;
        $baseGiftcreditHiddenTaxRefunded = 0;

        $totalGiftvoucherHiddenTax = 0;
        $baseTotalGiftvoucherHiddenTax = 0;
        $baseTotalGiftcreditHiddenTax = 0;
        $totalGiftcreditHiddenTax = 0;

        foreach ($order->getCreditmemosCollection() as $existedCreditmemo) {
            if ($existedCreditmemo->getSimigiftVoucherDiscount() || $existedCreditmemo->getSimiuseGiftCreditAmount()) {
                $totalGiftvoucherDiscountRefunded += $existedCreditmemo->getSimigiftVoucherDiscount();
                $baseGiftvoucherTotalDiscountRefunded += $existedCreditmemo->getSimibaseGiftVoucherDiscount();
                $totalGiftcreditDiscountRefunded += $existedCreditmemo->getSimiuseGiftCreditAmount();
                $baseGiftcreditTotalDiscountRefunded += $existedCreditmemo->getSimibaseUseGiftCreditAmount();

                $hiddenGiftvoucherTaxRefunded += $existedCreditmemo->getSimigiftvoucherHiddenTaxAmount();
                $baseGiftvoucherHiddenTaxRefunded += $existedCreditmemo->getSimigiftvoucherBaseHiddenTaxAmount();
                $hiddenGiftcreditTaxRefunded += $existedCreditmemo->getSimigiftcreditHiddenTaxAmount();
                $baseGiftcreditHiddenTaxRefunded += $existedCreditmemo->getSimigiftcreditBaseHiddenTaxAmount();
            }
        }

        $baseShippingAmount = $creditmemo->getBaseShippingAmount();
        if ($baseShippingAmount) {
            $baseTotalDiscountAmountGiftvoucher = $baseTotalDiscountAmountGiftvoucher + ($baseShippingAmount * 
                $order->getSimibaseGiftVoucherDiscountForShipping() / $order->getBaseShippingAmount());
            $totalDiscountAmountGiftvoucher = $totalDiscountAmountGiftvoucher + ($order->getShippingAmount() * 
                $baseTotalDiscountAmountGiftvoucher / $order->getBaseShippingAmount() );
            $baseTotalDiscountAmountCredit = $baseTotalDiscountAmountCredit + ($baseShippingAmount * 
                $order->getSimibaseGiftcreditDiscountForShipping() / $order->getBaseShippingAmount());
            $totalDiscountAmountCredit = $totalDiscountAmountCredit + ($order->getShippingAmount() * 
                $baseTotalDiscountAmountCredit / $order->getBaseShippingAmount());

            $baseTotalGiftvoucherHiddenTax = $baseShippingAmount 
                * $order->getGiftvoucherBaseShippingHiddenTaxAmount() / $order->getBaseShippingAmount();
            $totalGiftvoucherHiddenTax = $order->getSimiGiftvoucherShippingHiddenTaxAmount()
                * $baseTotalGiftvoucherHiddenTax / $order->getBaseShippingAmount();
            $baseTotalGiftcreditHiddenTax = $baseShippingAmount 
                * $order->getGiftcreditBaseShippingHiddenTaxAmount() / $order->getBaseShippingAmount();
            $totalGiftcreditHiddenTax = $order->getSimigiftcreditShippingHiddenTaxAmount()
                * $baseTotalGiftcreditHiddenTax / $order->getBaseShippingAmount();
        }

        if ($this->isLast($creditmemo)) {
            $baseTotalDiscountAmountGiftvoucher = $order->getSimibaseGiftVoucherDiscount()
                - $baseGiftvoucherTotalDiscountRefunded;
            $totalDiscountAmountGiftvoucher = $order->getSimigiftVoucherDiscount() - $totalGiftvoucherDiscountRefunded;
            $baseTotalDiscountAmountCredit = $order->getSimibaseUseGiftCreditAmount()
                - $baseGiftcreditTotalDiscountRefunded;
            $totalDiscountAmountCredit = $order->getSimiuseGiftCreditAmount() - $totalGiftcreditDiscountRefunded;

            $totalGiftvoucherHiddenTax = $order->getSimigiftvoucherHiddenTaxAmount() - $hiddenGiftvoucherTaxRefunded;
            $baseTotalGiftvoucherHiddenTax = $order->getSimigiftvoucherBaseHiddenTaxAmount()
                - $baseGiftvoucherHiddenTaxRefunded;
            $totalGiftcreditHiddenTax = $order->getSimigiftcreditHiddenTaxAmount() - $hiddenGiftcreditTaxRefunded;
            $baseTotalGiftcreditHiddenTax = $order->getSimigiftcreditBaseHiddenTaxAmount()
                - $baseGiftcreditHiddenTaxRefunded;
        } else {
            foreach ($creditmemo->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy()) {
                    continue;
                }

                $orderItemDiscountGiftvoucher = (float) $orderItem->getSimigiftVoucherDiscount();
                $baseOrderItemDiscountGiftvoucher = (float) $orderItem->getSimibaseGiftVoucherDiscount();
                $orderItemDiscountCredit = (float) $orderItem->getSimiuseGiftCreditAmount();
                $baseOrderItemDiscountCredit = (float) $orderItem->getSimibaseUseGiftCreditAmount();

                $orderItemGiftvoucherHiddenTax = (float) $orderItem->getSimigiftvoucherHiddenTaxAmount();
                $baseOrderItemGiftvoucherHiddenTax = (float) $orderItem->getSimigiftvoucherBaseHiddenTaxAmount();
                $orderItemGiftcreditHiddenTax = (float) $orderItem->getSimigiftcreditHiddenTaxAmount();
                $baseOrderItemGiftcreditHiddenTax = (float) $orderItem->getSimigiftcreditBaseHiddenTaxAmount();

                $orderItemQty = $orderItem->getQtyOrdered();
                $creditmemoItemQty = $item->getQty();

                if ($orderItemDiscountGiftvoucher && $orderItemQty) {
                    if (version_compare(Mage::getVersion(), '1.7.0.0', '>=')) {
                        $discount = $creditmemo->roundPrice(
                            $orderItemDiscountGiftvoucher / $orderItemQty * $creditmemoItemQty, 'regular', true);
                        $baseDiscount = $creditmemo->roundPrice(
                            $baseOrderItemDiscountGiftvoucher / $orderItemQty * $creditmemoItemQty, 'base', true);
                    } else {
                        $discount = $orderItemDiscountGiftvoucher / $orderItemQty * $creditmemoItemQty;
                        $baseDiscount = $baseOrderItemDiscountGiftvoucher / $orderItemQty * $creditmemoItemQty;
                    }
                    $totalDiscountAmountGiftvoucher += $discount;
                    $baseTotalDiscountAmountGiftvoucher += $baseDiscount;

                    if (version_compare(Mage::getVersion(), '1.7.0.0', '>=')) {
                        $totalGiftvoucherHiddenTax += $creditmemo->roundPrice(
                            $orderItemGiftvoucherHiddenTax / $orderItemQty * $creditmemoItemQty, 'regular', true);
                        $baseTotalGiftvoucherHiddenTax += $creditmemo->roundPrice(
                            $baseOrderItemGiftvoucherHiddenTax / $orderItemQty * $creditmemoItemQty, 'base', true);
                    } else {
                        $totalGiftvoucherHiddenTax += $orderItemGiftvoucherHiddenTax / $orderItemQty 
                            * $creditmemoItemQty;
                        $baseTotalGiftvoucherHiddenTax += $baseOrderItemGiftvoucherHiddenTax / $orderItemQty 
                            * $creditmemoItemQty;
                    }
                }
                if ($orderItemDiscountCredit && $orderItemQty) {
                    if (version_compare(Mage::getVersion(), '1.7.0.0', '>=')) {
                        $discount = $creditmemo->roundPrice(
                            $orderItemDiscountCredit / $orderItemQty * $creditmemoItemQty, 'regular', true);
                        $baseDiscount = $creditmemo->roundPrice(
                            $baseOrderItemDiscountCredit / $orderItemQty * $creditmemoItemQty, 'base', true);
                    } else {
                        $discount = $orderItemDiscountCredit / $orderItemQty * $creditmemoItemQty;
                        $baseDiscount = $baseOrderItemDiscountCredit / $orderItemQty * $creditmemoItemQty;
                    }
                    $totalDiscountAmountCredit += $discount;
                    $baseTotalDiscountAmountCredit += $baseDiscount;

                    if (version_compare(Mage::getVersion(), '1.7.0.0', '>=')) {
                        $totalGiftcreditHiddenTax += $creditmemo->roundPrice(
                            $orderItemGiftcreditHiddenTax / $orderItemQty * $creditmemoItemQty, 'regular', true);
                        $baseTotalGiftcreditHiddenTax += $creditmemo->roundPrice(
                            $baseOrderItemGiftcreditHiddenTax / $orderItemQty * $creditmemoItemQty, 'base', true);
                    } else {
                        $totalGiftcreditHiddenTax += $orderItemGiftcreditHiddenTax / $orderItemQty 
                            * $creditmemoItemQty;
                        $baseTotalGiftcreditHiddenTax += $baseOrderItemGiftcreditHiddenTax / $orderItemQty 
                            * $creditmemoItemQty;
                    }
                }
            }
            $allowedGiftvoucherBaseHiddenTax = $order->getSimigiftvoucherHiddenTaxAmount() - $hiddenGiftvoucherTaxRefunded;
            $allowedGiftvoucherHiddenTax = $order->getSimigiftvoucherBaseHiddenTaxAmount()
                - $baseGiftvoucherHiddenTaxRefunded;
            $allowedGiftcreditBaseHiddenTax = $order->getSimigiftcreditHiddenTaxAmount() - $hiddenGiftcreditTaxRefunded;
            $allowedGiftcreditHiddenTax = $order->getSimigiftcreditBaseHiddenTaxAmount()
                - $baseGiftcreditHiddenTaxRefunded;

            $totalGiftvoucherHiddenTax = min($allowedGiftvoucherBaseHiddenTax, $totalGiftvoucherHiddenTax);
            $baseTotalGiftvoucherHiddenTax = min($allowedGiftvoucherHiddenTax, $baseTotalGiftvoucherHiddenTax);
            $totalGiftcreditHiddenTax = min($allowedGiftcreditBaseHiddenTax, $totalGiftcreditHiddenTax);
            $baseTotalGiftcreditHiddenTax = min($allowedGiftcreditHiddenTax, $baseTotalGiftcreditHiddenTax);
        }

        $creditmemo->setSimibaseGiftVoucherDiscount($baseTotalDiscountAmountGiftvoucher);
        $creditmemo->setSimigiftVoucherDiscount($totalDiscountAmountGiftvoucher);

        $creditmemo->setSimibaseUseGiftCreditAmount($baseTotalDiscountAmountCredit);
        $creditmemo->setSimiuseGiftCreditAmount($totalDiscountAmountCredit);

        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseTotalDiscountAmountCredit 
            - $baseTotalDiscountAmountGiftvoucher + $totalGiftvoucherHiddenTax + $totalGiftcreditHiddenTax);
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $totalDiscountAmountCredit 
            - $totalDiscountAmountGiftvoucher + $baseTotalGiftvoucherHiddenTax + $baseTotalGiftcreditHiddenTax);
    }

    /**
     * Check credit memo is last or not
     * 
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return boolean
     */
    public function isLast($creditmemo)
    {
        foreach ($creditmemo->getAllItems() as $item) {
            if (!$item->isLast()) {
                return false;
            }
        }
        return true;
    }

}
