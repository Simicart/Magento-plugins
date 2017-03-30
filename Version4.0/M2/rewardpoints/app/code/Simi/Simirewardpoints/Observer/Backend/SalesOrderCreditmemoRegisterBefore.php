<?php

namespace Simi\Simirewardpoints\Observer\Backend;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderCreditmemoRegisterBefore implements ObserverInterface
{

    /**
     * PriceCurrencyInterface
     *
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Simi\Simirewardpoints\Helper\Data
     */
    protected $_transaction;

    /**
     * SalesOrderInvoiceSaveAfter constructor.
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Simi\Simirewardpoints\Helper\Action $action
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\App\RequestInterface $request,
        \Simi\Simirewardpoints\Model\TransactionFactory $transaction
    ) {
        $this->_priceCurrency = $priceCurrency;
        $this->_request = $request;
        $this->_transaction = $transaction;
    }

    /**
     * Set Final Price to product in product list
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_request->getActionName() == "updateQty") {
            return $this;
        }

        $creditmemo = $observer['creditmemo'];

        $input = $this->_request->getParam('creditmemo');
        $order = $creditmemo->getOrder();

        // Refund point to customer (that he used to spend)
        if (isset($input['refund_points']) && $input['refund_points'] > 0) {
            $refundPoints = (int) $input['refund_points'];

            $maxPoint = $order->getSimiRewardpointsSpent();
            $maxPoint -= (int) $this->_transaction->create()->getCollection()
                            ->addFieldToFilter('action', 'spending_creditmemo')
                            ->addFieldToFilter('order_id', $order->getId())
                            ->getFieldTotal();

            $refundPoints = min($refundPoints, $maxPoint);
            $creditmemo->setSimiRefundSpentPoints(max($refundPoints, 0));
        }

        // Deduce point from customer (that earned from this order)
        if (isset($input['refund_earned_points']) && $input['refund_earned_points'] > 0) {
            $refundPoints = (int) $input['refund_earned_points'];

            $maxEarnedRefund = (int) $this->_transaction->create()->getCollection()
                            ->addFieldToFilter('action', 'earning_invoice')
                            ->addFieldToFilter('order_id', $order->getId())
                            ->getFieldTotal();
            if ($maxEarnedRefund > $order->getSimiRewardpointsEarn()) {
                $maxEarnedRefund = $order->getSimiRewardpointsEarn();
            }
            $maxEarnedRefund += (int) $this->_transaction->create()->getCollection()
                            ->addFieldToFilter('action', 'earning_creditmemo')
                            ->addFieldToFilter('order_id', $order->getId())
                            ->getFieldTotal();
            if ($maxEarnedRefund > $order->getSimiRewardpointsEarn()) {
                $maxEarnedRefund = $order->getSimiRewardpointsEarn();
            }
            $refundPoints = min($refundPoints, $maxEarnedRefund);
            $creditmemo->setSimiRefundEarnedPoints(max($refundPoints, 0));
            $creditmemo->setSimiRewardpointsEarn($creditmemo->getSimiRefundEarnedPoints()); //Hai.Tran
        }
        //Brian allow creditmemo when creditmemo total equal zero
        if ($order->getSimiRewardpointsSpent() > 0 && $this->_priceCurrency->round($creditmemo->getGrandTotal()) <= 0
        ) {
            $creditmemo->setAllowZeroGrandTotal(true);
        }

        return $this;
    }
}
