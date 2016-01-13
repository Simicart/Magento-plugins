<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpoints Spend for Order by Point Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Model_Total_Invoice_Point extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect total when create Invoice
     * 
     * @param Mage_Sales_Model_Order_Invoice $invoice
     */
    public function isLast($invoice){
        foreach ($invoice->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            if ($orderItem->isDummy()) {
                continue;
            }
            if (!$item->isLast()) {
                return false;
            }
        }
        return true;
    }
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();
		/**
         * update 2.0
         */
        $earnPoint = 0;
        $maxEarn  = $order->getRewardpointsEarn();
        $maxEarn -= (int)Mage::getResourceModel('rewardpoints/transaction_collection')
            ->addFieldToFilter('action', 'earning_invoice')
            ->addFieldToFilter('order_id', $order->getId())
            ->getFieldTotal();
        if ($maxEarn >= 0) {
            foreach ($invoice->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy()) {
                    continue;
                }
                $itemPoint  = (int)$orderItem->getRewardpointsEarn();
                $itemPoint  = $itemPoint * $item->getQty() / $orderItem->getQtyOrdered();
                $earnPoint += floor($itemPoint);
            }
            // Hiepdd add shipping earning points for the first invoice 
            $earnPoint += Mage::helper('rewardpoints/calculation_earning')->getShippingEarningPoints($order);
            if($this->isLast($invoice) || $earnPoint >= $maxEarn) $earnPoint = $maxEarn;
            $invoice->setRewardpointsEarn($earnPoint);
        }
        
        if ($order->getRewardpointsDiscount() < 0.0001) {
            return ;
        }
        
        $invoice->setRewardpointsDiscount(0);
        $invoice->setRewardpointsBaseDiscount(0);

        $totalDiscountAmount     = 0;
        $baseTotalDiscountAmount = 0;
        
        $totalDiscountInvoiced     = 0;
        $baseTotalDiscountInvoiced = 0;
        
//        $hiddenTaxInvoiced      = 0;
//        $baseHiddenTaxInvoiced  = 0;
//        
//        $totalHiddenTax      = 0;
//        $baseTotalHiddenTax  = 0;

        /**
         * Checking if shipping discount was added in previous invoices.
         * So basically if we have invoice with positive discount and it
         * was not canceled we don't add shipping discount to this one.
         */
        $addShippingDicount = true;
        foreach ($order->getInvoiceCollection() as $previusInvoice) {
            if ($previusInvoice->getRewardpointsDiscount()) {
                $addShippingDicount = false;
                $totalDiscountInvoiced     += $previusInvoice->getRewardpointsDiscount();
                $baseTotalDiscountInvoiced += $previusInvoice->getRewardpointsBaseDiscount();
                
//                $hiddenTaxInvoiced      += $previusInvoice->getRewardpointsHiddenTaxAmount();
//                $baseHiddenTaxInvoiced  += $previusInvoice->getRewardpointsBaseHiddenTaxAmount();
            }
        }

        if ($addShippingDicount) {
            $totalDiscountAmount     += $order->getRewardpointsAmount();
            $baseTotalDiscountAmount += $order->getRewardpointsBaseAmount();
            
//            $totalHiddenTax      += $order->getRewardpointsShippingHiddenTaxAmount();
//            $baseTotalHiddenTax  += $order->getRewardpointsBaseShippingHiddenTaxAmount();
        }
        
        if ($this->isLast($invoice)) {
            $totalDiscountAmount     = $order->getRewardpointsDiscount() - $totalDiscountInvoiced;
            $baseTotalDiscountAmount = $order->getRewardpointsBaseDiscount() - $baseTotalDiscountInvoiced;
            
//            $totalHiddenTax      = $order->getRewardpointsHiddenTaxAmount() - $hiddenTaxInvoiced;
//            $baseTotalHiddenTax  = $order->getRewardpointsBaseHiddenTaxAmount() - $baseHiddenTaxInvoiced;
        } else {
            /** @var $item Mage_Sales_Model_Order_Invoice_Item */
            foreach ($invoice->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy()) {
                     continue;
                }

                $orderItemDiscount      = (float) $orderItem->getRewardpointsDiscount();
                $baseOrderItemDiscount  = (float) $orderItem->getRewardpointsBaseDiscount();
                
//                $orderItemHiddenTax      = (float) $orderItem->getRewardpointsHiddenTaxAmount();
//                $baseOrderItemHiddenTax  = (float) $orderItem->getRewardpointsBaseHiddenTaxAmount();
                
                $orderItemQty       = $orderItem->getQtyOrdered();

                if ($orderItemDiscount && $orderItemQty) {
                    $totalDiscountAmount += $invoice->roundPrice($orderItemDiscount / $orderItemQty * $item->getQty(), 'regular', true);
                    $baseTotalDiscountAmount += $invoice->roundPrice($baseOrderItemDiscount / $orderItemQty * $item->getQty(), 'base', true);
                    
//                    $totalHiddenTax += $invoice->roundPrice($orderItemHiddenTax / $orderItemQty * $item->getQty(), 'regular', true);
//                    $baseTotalHiddenTax += $invoice->roundPrice($baseOrderItemHiddenTax / $orderItemQty * $item->getQty(), 'base', true);
                }
            }
//            $allowedBaseHiddenTax   = $order->getRewardpointsBaseHiddenTaxAmount() - $baseHiddenTaxInvoiced;
//            $allowedHiddenTax       = $order->getRewardpointsHiddenTaxAmount() - $hiddenTaxInvoiced;
//            
//            $totalHiddenTax     = min($allowedHiddenTax, $totalHiddenTax);
//            $baseTotalHiddenTax = min($allowedBaseHiddenTax, $baseTotalHiddenTax);
        }
        
        $invoice->setRewardpointsDiscount($totalDiscountAmount);
        $invoice->setRewardpointsBaseDiscount($baseTotalDiscountAmount);
        
//        $invoice->setRewardpointsHiddenTaxAmount($totalHiddenTax);
//        $invoice->setRewardpointsBaseHiddenTaxAmount($baseTotalHiddenTax);

        $invoice->setGrandTotal($invoice->getGrandTotal() - $totalDiscountAmount);// + $totalHiddenTax);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalDiscountAmount);// + $baseTotalHiddenTax);
        return $this;
    }
}
