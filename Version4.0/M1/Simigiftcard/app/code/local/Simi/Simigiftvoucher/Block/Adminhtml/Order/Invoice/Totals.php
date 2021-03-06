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
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Order_Invoice_Totals
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Order_Invoice_Totals extends Mage_Adminhtml_Block_Sales_Order_Totals_Item {

    /**
     *
     */
    public function initTotals() {
        $orderTotalsBlock = $this->getParentBlock();
        $order = $orderTotalsBlock->getInvoice();
        if ($order->getSimigiftVoucherDiscount() && $order->getSimigiftVoucherDiscount() > 0) {
            $orderTotalsBlock->addTotal(new Varien_Object(array(
                'code' => 'simigiftvoucher',
                'label' => $this->__('Simi Gift Card (%s)', $order->getOrder()->getSimigiftCodes()),
                'value' => -$order->getSimigiftVoucherDiscount(),
                'base_value' => -$order->getSimibaseGiftVoucherDiscount(),
                    )), 'subtotal');
        }
    }

}