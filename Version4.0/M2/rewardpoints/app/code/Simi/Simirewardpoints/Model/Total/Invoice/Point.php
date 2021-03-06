<?php

/**
 * Simirewardpoints Spend for Order by Point Model
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Magestore Developer
 */

namespace Simi\Simirewardpoints\Model\Total\Invoice;

class Point extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{

    /**
     * @var \Simi\Simirewardpoints\Helper\Calculation\Earning
     */
    protected $_helperEarning;

    /**
     * @var \Simi\Simirewardpoints\Helper\Data
     */
    protected $_transaction;

    /**
     * @var \Simi\Simirewardpoints\Model\transactionFactory
     */
    protected $_storeManager;

    public function __construct(
        \Simi\Simirewardpoints\Helper\Calculation\Earning $helperEarning,
        \Simi\Simirewardpoints\Model\TransactionFactory $transaction,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($data);
        $this->_helperEarning = $helperEarning;
        $this->_transaction = $transaction;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $order = $invoice->getOrder();
        $invoiceCollection = $order->getInvoiceCollection();
        /** Earning point * */
        $earnPoint = 0;
        $maxEarn = $order->getSimirewardpointsEarn();
        $maxEarn -= (int) $this->_transaction->create()->getCollection()
                        ->addFieldToFilter('action', 'earning_invoice')
                        ->addFieldToFilter('order_id', $order->getId())
                        ->getFieldTotal();
        if ($maxEarn >= 0) {
            foreach ($invoice->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy()) {
                    continue;
                }
                $earnPoint += floor((int) $orderItem->getSimirewardpointsEarn() * $item->getQty() / $orderItem->getQtyOrdered());
            }
            if ($invoiceCollection->getSize() == 0) {
                $earnPoint += $this->_helperEarning->getShippingEarningPoints($order);
            }
            if ($this->isLast($invoice)) {
                $earnPoint = $maxEarn;
            }
        }
        if ($earnPoint > 0) {
            $invoice->setSimirewardpointsEarn($earnPoint);
        }
        /** End earningn point * */
        /** Spending point * */
        if ($order->getSimirewardpointsDiscount() < 0.0001) {
            return;
        }

        $totalDiscountAmount = 0;
        $baseTotalDiscountAmount = 0;
        $totalDiscountInvoiced = 0;
        $baseTotalDiscountInvoiced = 0;

        /**
         * Checking if shipping discount was added in previous invoices.
         * So basically if we have invoice with positive discount and it
         * was not canceled we don't add shipping discount to this one.
         */
        $addShippingDicount = true;
        foreach ($invoiceCollection as $previusInvoice) {
            if ($previusInvoice->getSimirewardpointsDiscount()) {
                $addShippingDicount = false;
                $totalDiscountInvoiced += $previusInvoice->getSimirewardpointsDiscount();
                $baseTotalDiscountInvoiced += $previusInvoice->getSimirewardpointsBaseDiscount();
            }
        }
        if ($addShippingDicount) {
            $totalDiscountAmount = $order->getSimirewardpointsAmount();
            $baseTotalDiscountAmount = $order->getSimirewardpointsBaseAmount();
        }
        if ($this->isLast($invoice)) {
            $totalDiscountAmount = $order->getSimirewardpointsDiscount() - $totalDiscountInvoiced;
            $baseTotalDiscountAmount = $order->getSimirewardpointsBaseDiscount() - $baseTotalDiscountInvoiced;
        } else {
            /** @var $item Mage_Sales_Model_Order_Invoice_Item */
            foreach ($invoice->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy()) {
                    continue;
                }
                $orderItemDiscount = (float) $orderItem->getSimirewardpointsDiscount();
                $baseOrderItemDiscount = (float) $orderItem->getSimirewardpointsBaseDiscount();
                $orderItemQty = $orderItem->getQtyOrdered();
                if ($orderItemDiscount && $orderItemQty) {
                    $totalDiscountAmount += $invoice->roundPrice($orderItemDiscount / $orderItemQty * $item->getQty(), 'regular', true);
                    $baseTotalDiscountAmount += $invoice->roundPrice($baseOrderItemDiscount / $orderItemQty * $item->getQty(), 'base', true);
                }
            }
        }

        $invoice->setSimirewardpointsDiscount($totalDiscountAmount);
        $invoice->setSimirewardpointsBaseDiscount($baseTotalDiscountAmount);

        $invoice->setGrandTotal($invoice->getGrandTotal() - $totalDiscountAmount);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalDiscountAmount);
        /** End spending point * */
        return $this;
    }

    public function isLast($invoice)
    {
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
}
