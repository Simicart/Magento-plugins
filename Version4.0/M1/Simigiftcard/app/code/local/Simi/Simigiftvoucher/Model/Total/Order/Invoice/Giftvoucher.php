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
 * Class Simi_Simigiftvoucher_Model_Total_Order_Invoice_Giftvoucher
 */
class Simi_Simigiftvoucher_Model_Total_Order_Invoice_Giftvoucher extends Mage_Sales_Model_Order_Invoice_Total_Abstract {

    /**
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return $this
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice) {
        $order = $invoice->getOrder();
        if ($order->getPayment()->getMethod() == 'simigiftvoucher') {
            return $this;
        }
        if (!$order->getSimigiftVoucherDiscount() && !$order->getSimiuseGiftCreditAmount()) {
            return $this;
        }

        $invoice->setSimiuseGiftCreditAmount(0);
        $invoice->setSimibaseUseGiftCreditAmount(0);
        $invoice->setSimibaseGiftVoucherDiscount(0);
        $invoice->setSimigiftVoucherDiscount(0);

        $totalDiscountAmountGiftvoucher = 0;
        $baseTotalDiscountAmountGiftvoucher = 0;
        $totalDiscountAmountCredit = 0;
        $baseTotalDiscountAmountCredit = 0;

        $totalGiftvoucherDiscountInvoiced = 0;
        $baseTotalGiftvoucherDiscountInvoiced = 0;
        $totalGiftcreditDiscountInvoiced = 0;
        $baseTotalGiftcreditDiscountInvoiced = 0;

        $hiddenGiftvoucherTaxInvoiced = 0;
        $baseHiddenGiftvoucherTaxInvoiced = 0;
        $hiddenGiftcreditTaxInvoiced = 0;
        $baseHiddenGiftcreditTaxInvoiced = 0;

        $totalGiftvoucherHiddenTax = 0;
        $baseTotalGiftvoucherHiddenTax = 0;
        $totalGiftcreditHiddenTax = 0;
        $baseTotalGiftcreditHiddenTax = 0;

        $addShippingDicount = true;
        foreach ($invoice->getOrder()->getInvoiceCollection() as $previusInvoice) {
            if ($previusInvoice->getSimigiftVoucherDiscount() || $previusInvoice->getSimiuseGiftCreditAmount()) {
                $addShippingDicount = false;

                $totalGiftvoucherDiscountInvoiced += $previusInvoice->getSimigiftVoucherDiscount();
                $baseTotalGiftvoucherDiscountInvoiced += $previusInvoice->getSimibaseGiftVoucherDiscount();
                $totalGiftcreditDiscountInvoiced += $previusInvoice->getSimiuseGiftCreditAmount();
                $baseTotalGiftcreditDiscountInvoiced += $previusInvoice->getSimibaseUseGiftCreditAmount();

                $hiddenGiftvoucherTaxInvoiced += $previusInvoice->getSimigiftvoucherHiddenTaxAmount();
                $baseHiddenGiftvoucherTaxInvoiced += $previusInvoice->getSimigiftvoucherBaseHiddenTaxAmount();
                $hiddenGiftcreditTaxInvoiced += $previusInvoice->getSimigiftcreditHiddenTaxAmount();
                $baseHiddenGiftcreditTaxInvoiced += $previusInvoice->getSimigiftcreditBaseHiddenTaxAmount();
            }
        }


        if ($addShippingDicount) {
            $totalDiscountAmountGiftvoucher = $totalDiscountAmountGiftvoucher + $order->getSimigiftVoucherDiscountForShipping();
            $baseTotalDiscountAmountGiftvoucher = $baseTotalDiscountAmountGiftvoucher + $order->getSimibaseGiftVoucherDiscountForShipping();
            $totalDiscountAmountCredit = $totalDiscountAmountCredit + $order->getSimigiftcreditDiscountForShipping();
            $baseTotalDiscountAmountCredit = $baseTotalDiscountAmountCredit + $order->getSimibaseGiftcreditDiscountForShipping();

            $totalGiftvoucherHiddenTax += $order->getSimigiftvoucherShippingHiddenTaxAmount();
            $baseTotalGiftvoucherHiddenTax += $order->getgiftvoucherBaseShippingHiddenTaxAmount();
            $totalGiftcreditHiddenTax += $order->getSimigiftcreditShippingHiddenTaxAmount();
            $baseTotalGiftcreditHiddenTax += $order->getgiftcreditBaseShippingHiddenTaxAmount();
        }


        if ($invoice->isLast()) {
            $totalDiscountAmountGiftvoucher = $order->getSimigiftVoucherDiscount() - $totalGiftvoucherDiscountInvoiced;
            $baseTotalDiscountAmountGiftvoucher = $order->getSimibaseGiftVoucherDiscount() - $baseTotalGiftvoucherDiscountInvoiced;
            $totalDiscountAmountCredit = $order->getSimiuseGiftCreditAmount() - $totalGiftcreditDiscountInvoiced;
            $baseTotalDiscountAmountCredit = $order->getSimibaseUseGiftCreditAmount() - $baseTotalGiftcreditDiscountInvoiced;

            $totalGiftvoucherHiddenTax = $order->getSimigiftvoucherHiddenTaxAmount() - $hiddenGiftvoucherTaxInvoiced;
            $baseTotalGiftvoucherHiddenTax = $order->getSimigiftvoucherBaseHiddenTaxAmount() - $baseHiddenGiftvoucherTaxInvoiced;
            $totalGiftcreditHiddenTax = $order->getSimigiftcreditHiddenTaxAmount() - $hiddenGiftcreditTaxInvoiced;
            $baseTotalGiftcreditHiddenTax = $order->getSimigiftcreditBaseHiddenTaxAmount() - $baseHiddenGiftcreditTaxInvoiced;
        } else {
            foreach ($invoice->getAllItems() as $item) {
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
                $invoiceItemQty = $item->getQty();

                if ($orderItemDiscountGiftvoucher && $orderItemQty) {
                    if (version_compare(Mage::getVersion(), '1.7.0.0', '>=')) {
                        $discount = $invoice->roundPrice($orderItemDiscountGiftvoucher / $orderItemQty * $invoiceItemQty, 'regular', true);
                        $baseDiscount = $invoice->roundPrice($baseOrderItemDiscountGiftvoucher / $orderItemQty * $invoiceItemQty, 'base', true);
                    } else {
                        $discount = $orderItemDiscountGiftvoucher / $orderItemQty * $invoiceItemQty;
                        $baseDiscount = $baseOrderItemDiscountGiftvoucher / $orderItemQty * $invoiceItemQty;
                    }
                    $totalDiscountAmountGiftvoucher += $discount;
                    $baseTotalDiscountAmountGiftvoucher += $baseDiscount;

					if (version_compare(Mage::getVersion(), '1.7.0.0', '>=')) {
						$totalGiftvoucherHiddenTax += $invoice->roundPrice($orderItemGiftvoucherHiddenTax / $orderItemQty * $invoiceItemQty, 'regular', true);
						$baseTotalGiftvoucherHiddenTax += $invoice->roundPrice($baseOrderItemGiftvoucherHiddenTax / $orderItemQty * $invoiceItemQty, 'base', true);
					} else {
						$totalGiftvoucherHiddenTax += $orderItemGiftvoucherHiddenTax / $orderItemQty * $invoiceItemQty;
						$baseTotalGiftvoucherHiddenTax += $baseOrderItemGiftvoucherHiddenTax / $orderItemQty * $invoiceItemQty;
					}
                }
                if ($orderItemDiscountCredit && $orderItemQty) {
                    if (version_compare(Mage::getVersion(), '1.7.0.0', '>=')) {
                        $discount = $invoice->roundPrice($orderItemDiscountCredit / $orderItemQty * $invoiceItemQty, 'regular', true);
                        $baseDiscount = $invoice->roundPrice($baseOrderItemDiscountCredit / $orderItemQty * $invoiceItemQty, 'base', true);
                    } else {
                        $discount = $orderItemDiscountCredit / $orderItemQty * $invoiceItemQty;
                        $baseDiscount = $baseOrderItemDiscountCredit / $orderItemQty * $invoiceItemQty;
                    }
                    $totalDiscountAmountCredit += $discount;
                    $baseTotalDiscountAmountCredit += $baseDiscount;

					if (version_compare(Mage::getVersion(), '1.7.0.0', '>=')) {
						$totalGiftcreditHiddenTax += $invoice->roundPrice($orderItemGiftcreditHiddenTax / $orderItemQty * $invoiceItemQty, 'regular', true);
						$baseTotalGiftcreditHiddenTax += $invoice->roundPrice($baseOrderItemGiftcreditHiddenTax / $orderItemQty * $invoiceItemQty, 'base', true);
					} else {
						$totalGiftcreditHiddenTax += $orderItemGiftcreditHiddenTax / $orderItemQty * $invoiceItemQty;
						$baseTotalGiftcreditHiddenTax += $baseOrderItemGiftcreditHiddenTax / $orderItemQty * $invoiceItemQty;
					}
                }
            }

            $allowedGiftvoucherBaseHiddenTax = $order->getSimigiftvoucherHiddenTaxAmount() - $hiddenGiftvoucherTaxInvoiced;
            $allowedGiftvoucherHiddenTax = $order->getSimigiftvoucherBaseHiddenTaxAmount() - $baseHiddenGiftvoucherTaxInvoiced;
            $allowedGiftcreditBaseHiddenTax = $order->getSimigiftcreditHiddenTaxAmount() - $hiddenGiftcreditTaxInvoiced;
            $allowedGiftcreditHiddenTax = $order->getSimigiftcreditBaseHiddenTaxAmount() - $baseHiddenGiftcreditTaxInvoiced;

            $totalGiftvoucherHiddenTax = min($allowedGiftvoucherBaseHiddenTax, $totalGiftvoucherHiddenTax);
            $baseTotalGiftvoucherHiddenTax = min($allowedGiftvoucherHiddenTax, $baseTotalGiftvoucherHiddenTax);
            $totalGiftcreditHiddenTax = min($allowedGiftcreditBaseHiddenTax, $totalGiftcreditHiddenTax);
            $baseTotalGiftcreditHiddenTax = min($allowedGiftcreditHiddenTax, $baseTotalGiftcreditHiddenTax);
        }

        // Zend_debug::dump($totalGiftvoucherHiddenTax + $totalGiftcreditHiddenTax);die();		
        // $invoice->setSubtotal($invoice->getSubtotal() + $totalGiftvoucherHiddenTax + $totalGiftcreditHiddenTax);
        // $invoice->setBaseSubtotal($invoice->getBaseSubtotal() + $totalGiftvoucherHiddenTax + $totalGiftcreditHiddenTax);
        $invoice->setSubtotalInclTax($invoice->getSubtotalInclTax() + $totalGiftvoucherHiddenTax + $totalGiftcreditHiddenTax - $order->getSimiGiftvoucherShippingHiddenTaxAmount() - $order->getgetSimiGiftcreditDiscountForShipping());
        $invoice->setBaseSubtotalInclTax($invoice->getBaseSubtotalInclTax() + $totalGiftvoucherHiddenTax + $totalGiftcreditHiddenTax - $order->getSimibaseGiftVoucherDiscountForShipping() - $order->getSimibaseGiftcreditDiscountForShipping());

        $invoice->setSimibaseGiftVoucherDiscount($baseTotalDiscountAmountGiftvoucher);
        $invoice->setSimigiftVoucherDiscount($totalDiscountAmountGiftvoucher);

        $invoice->setSimibaseUseGiftCreditAmount($baseTotalDiscountAmountCredit);
        $invoice->setSimiuseGiftCreditAmount($totalDiscountAmountCredit);

        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalDiscountAmountCredit - $baseTotalDiscountAmountGiftvoucher + $totalGiftvoucherHiddenTax + $totalGiftcreditHiddenTax);
        $invoice->setGrandTotal($invoice->getGrandTotal() - $totalDiscountAmountCredit - $totalDiscountAmountGiftvoucher + $baseTotalGiftvoucherHiddenTax + $baseTotalGiftcreditHiddenTax);

        return $this;
    }

}
