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
 * Giftvoucher Order Totals Block
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */
class Simi_Simigiftvoucher_Block_Order_Totals extends Mage_Core_Block_Template
{

    public function initTotals()
    {

        $orderTotalsBlock = $this->getParentBlock();
        $order = $orderTotalsBlock->getOrder();

        if ($order->getSimigiftVoucherDiscount() && $order->getSimigiftVoucherDiscount() > 0) {
            $orderTotalsBlock->addTotal(new Varien_Object(array(
                'code' => 'simigiftvoucher',
                'label' => $this->__('Gift Card (%s)', $order->getSimigiftCodes()),
                'value' => -$order->getSimigiftVoucherDiscount(),
                'base_value' => -$order->getSimibaseGiftVoucherDiscount(),
                )), 'subtotal');
        }

        if ($refund = $this->getGiftCardRefund($order)) {
            $baseCurrency = Mage::app()->getStore($order->getStoreId())->getBaseCurrency();
            if ($rate = $baseCurrency->getRate($order->getOrderCurrencyCode())) {
                $refundAmount = $refund / $rate;
            }

            if ($order->getCustomerIsGuest() 
                || !Mage::helper('simigiftvoucher')->getGeneralConfig('enablecredit', $order->getStoreId())) {
                $label = $this->__('Refund to your Gift Card code');
            } else {
                $label = $this->__('Refund to your credit balance');
            }
            $orderTotalsBlock->addTotal(new Varien_Object(array(
                'code' => 'giftcard_refund',
                'label' => $label,
                'value' => $refund,
                'base_value' => $refundAmount,
                'area' => 'footer',
                )), 'subtotal');
        }
    }

    /**
     * Get Gift Card refunded amount
     *
     * @param Mage_Sales_Model_Order $order
     * @return float
     */
    public function getGiftCardRefund($order)
    {
        $refund = 0;
        foreach ($order->getCreditmemosCollection() as $creditmemo) {
            $refund += $creditmemo->getGiftcardRefundAmount();
        }
        return $refund;
    }

}
