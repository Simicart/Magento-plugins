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
class Magestore_RewardPoints_Model_Total_Creditmemo_Point extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * Collect total when create Creditmemo
     * 
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     */
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $creditmemo->setRewardpointsDiscount(0);
        $creditmemo->setRewardpointsBaseDiscount(0);

        $order = $creditmemo->getOrder();
        
        if ($order->getRewardpointsDiscount() < 0.0001) {
            return ;
        }

        $totalDiscountAmount = 0;
        $baseTotalDiscountAmount = 0;
        
        foreach ($order->getCreditmemosCollection() as $existedCreditmemo) {
            if ($existedCreditmemo->getRewardpointsDiscount()) {
                $totalDiscountRefunded     = $existedCreditmemo->getRewardpointsDiscount();
                $baseTotalDiscountRefunded = $existedCreditmemo->getRewardpointsBaseDiscount();
                
//                $hiddenTaxRefunded      = $existedCreditmemo->getRewardpointsHiddenTaxAmount();
//                $baseHiddenTaxRefunded  = $existedCreditmemo->getRewardpointsBaseHiddenTaxAmount();
            }
        }

        /**
         * Calculate how much shipping discount should be applied
         * basing on how much shipping should be refunded.
         */
        $baseShippingAmount = $creditmemo->getBaseShippingAmount();
        if ($baseShippingAmount) {
            $baseTotalDiscountAmount = $baseShippingAmount * $order->getRewardpointsBaseAmount() / $order->getBaseShippingAmount();
            $totalDiscountAmount = $order->getRewardpointsAmount() * $baseTotalDiscountAmount / $order->getBaseShippingAmount();
            
//            $baseTotalHiddenTax  = $baseShippingAmount * $order->getRewardpointsBaseShippingHiddenTaxAmount() / $order->getBaseShippingAmount();
//            $totalHiddenTax      = $order->getRewardpointsShippingHiddenTaxAmount() * $baseTotalHiddenTax / $order->getBaseShippingAmount();
        }
        
        if ($this->isLast($creditmemo)) {
            $baseTotalDiscountAmount   = $order->getRewardpointsBaseDiscount() - $baseTotalDiscountRefunded;
            $totalDiscountAmount       = $order->getRewardpointsDiscount() - $totalDiscountRefunded;
           
//            $totalHiddenTax      = $order->getRewardpointsHiddenTaxAmount() - $hiddenTaxRefunded;
//            $baseTotalHiddenTax  = $order->getRewardpointsBaseHiddenTaxAmount() - $baseHiddenTaxRefunded;
        } else {
            /** @var $item Mage_Sales_Model_Order_Invoice_Item */
            foreach ($creditmemo->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();

                if ($orderItem->isDummy()) {
                    continue;
                }

                $orderItemDiscount      = (float) $orderItem->getRewardpointsDiscount()*$orderItem->getQtyInvoiced()/$orderItem->getQtyOrdered();
                $baseOrderItemDiscount  = (float) $orderItem->getRewardpointsBaseDiscount()*$orderItem->getQtyInvoiced()/$orderItem->getQtyOrdered();
                
//                $orderItemHiddenTax      = (float) $orderItem->getRewardpointsHiddenTaxAmount();
//                $baseOrderItemHiddenTax  = (float) $orderItem->getRewardpointsBaseHiddenTaxAmount();
                
                $orderItemQty           = $orderItem->getQtyInvoiced();

                if ($orderItemDiscount && $orderItemQty) {                    
                    $totalDiscountAmount += $creditmemo->roundPrice($orderItemDiscount / $orderItemQty * $item->getQty(), 'regular', true);
                    $baseTotalDiscountAmount += $creditmemo->roundPrice($baseOrderItemDiscount / $orderItemQty * $item->getQty(), 'base', true);
                    
//                    $totalHiddenTax += $creditmemo->roundPrice($orderItemHiddenTax / $orderItemQty * $item->getQty(), 'regular', true);
//                    $baseTotalHiddenTax += $creditmemo->roundPrice($baseOrderItemHiddenTax / $orderItemQty * $item->getQty(), 'base', true);;
                }
            }
//            $allowedBaseHiddenTax   = $order->getRewardpointsBaseHiddenTaxAmount() - $baseHiddenTaxRefunded;
//            $allowedHiddenTax       = $order->getRewardpointsHiddenTaxAmount() - $hiddenTaxRefunded;
//            
//            $totalHiddenTax     = min($allowedHiddenTax, $totalHiddenTax);
//            $baseTotalHiddenTax = min($allowedBaseHiddenTax, $baseTotalHiddenTax);
        }

        $creditmemo->setRewardpointsDiscount($totalDiscountAmount);
        $creditmemo->setRewardpointsBaseDiscount($baseTotalDiscountAmount);
        
//        $creditmemo->setRewardpointsHiddenTaxAmount($totalHiddenTax);
//        $creditmemo->setRewardpointsBaseHiddenTaxAmount($baseTotalHiddenTax);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $totalDiscountAmount);// + $totalHiddenTax);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseTotalDiscountAmount);// + $baseTotalHiddenTax);
        return $this;
        
        
        
        
        $order = $creditmemo->getOrder();
        if ($order->getRewardpointsDiscount() < 0.0001) {
            return ;
        }
        if ($this->isLast($creditmemo)) {
            $baseDiscount   = $order->getRewardpointsBaseDiscount();
            $discount       = $order->getRewardpointsDiscount();
            foreach ($order->getCreditmemosCollection() as $existedCreditmemo) {
                if ($baseDiscount > 0.0001) {
                    $baseDiscount   -= $existedCreditmemo->getRewardpointsBaseDiscount();
                    $discount       -= $existedCreditmemo->getRewardpointsDiscount();
                }
            }
        } else {
            $orderTotal = $order->getGrandTotal() + $order->getRewardpointsDiscount();
            $ratio      = $creditmemo->getGrandTotal() / $orderTotal;
            $baseDiscount   = $order->getRewardpointsBaseDiscount() * $ratio;
            $discount       = $order->getRewardpointsDiscount() * $ratio;
            
            $maxBaseDiscount   = $order->getRewardpointsBaseDiscount();
            $maxDiscount       = $order->getRewardpointsDiscount();
            foreach ($order->getCreditmemosCollection() as $existedCreditmemo) {
                if ($maxBaseDiscount > 0.0001) {
                    $maxBaseDiscount    -= $existedCreditmemo->getRewardpointsBaseDiscount();
                    $maxDiscount        -= $existedCreditmemo->getRewardpointsDiscount();
                }
            }
            if ($baseDiscount > $maxBaseDiscount) {
                $baseDiscount   = $maxBaseDiscount;
                $discount       = $maxDiscount;
            }
        }
        
        if ($baseDiscount > 0.0001) {
            if ($creditmemo->getBaseGrandTotal() <= $baseDiscount) {
                $creditmemo->setRewardpointsBaseDiscount($creditmemo->getBaseGrandTotal());
                $creditmemo->setRewardpointsDiscount($creditmemo->getGrandTotal());
                $creditmemo->setBaseGrandTotal(0.0);
                $creditmemo->setGrandTotal(0.0);
            } else {
                $creditmemo->setRewardpointsBaseDiscount($baseDiscount);
                $creditmemo->setRewardpointsDiscount($discount);
                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseDiscount);
                $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $discount);
            }
            $creditmemo->setAllowZeroGrandTotal(true);
        }
    }
    
    /**
     * check credit memo is last or not
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
