<?php

/**
 * Simirewardpoints Total Point Earn/Spend Block
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Block\Totals\Creditmemo;

class Rewards extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Simi\Simirewardpoints\Helper\Point
     */
    public $_helperPoint;

    /**
     * @var \Simi\Simirewardpoints\Model\TransactionFactory
     */
    protected $_transaction;

    /**
     * @var \Simi\Simirewardpoints\Helper\Calculation\Earning
     */
    protected $_helperEarning;

    /**
     * Rewards constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Simi\Simirewardpoints\Helper\Point $helperPoint
     * @param \Simi\Simirewardpoints\Model\TransactionFactory $transaction
     * @param \Simi\Simirewardpoints\Helper\Calculation\Earning $helperEarning
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Simi\Simirewardpoints\Helper\Point $helperPoint,
        \Simi\Simirewardpoints\Model\TransactionFactory $transaction,
        \Simi\Simirewardpoints\Helper\Calculation\Earning $helperEarning,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_helperPoint = $helperPoint;
        $this->_transaction = $transaction;
        $this->_helperEarning = $helperEarning;
        parent::__construct($context, $data);
    }

    /**
     * get current creditmemo
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_coreRegistry->registry('current_creditmemo');
    }

    /**
     * check admin can refund point that customer spent
     *
     * @return boolean
     */
    public function canRefundPoints()
    {
        if ($this->getCreditmemo()->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        if ($this->getMaxPointRefund()) {
            return true;
        }
        return false;
    }

    /**
     * max point that admin can refund to customer
     *
     * @return int
     */
    public function getMaxPointRefund()
    {
        if ($this->hasData('max_point_refund')) {
            return $this->getData('max_point_refund');
        }
        $maxPointRefund = 0;
        if ($creditmemo = $this->getCreditmemo()) {
            $order = $creditmemo->getOrder();

            $maxPoint = $order->getSimirewardpointsSpent();
            $maxPointRefund = $maxPoint - (int) $this->_transaction->create()->getCollection()
                            ->addFieldToFilter('action', 'spending_creditmemo')
                            ->addFieldToFilter('order_id', $order->getId())
                            ->getFieldTotal();
            if ($creditmemo->getSimirewardpointsDiscount()) {
                $currentPoint = ceil($maxPoint * $creditmemo->getSimirewardpointsDiscount() / $order->getSimirewardpointsDiscount());
            } else {
                $currentPoint = 0;
            }
            $this->setData('total_point', $maxPoint);
            $this->setData('current_point', min($currentPoint, $maxPointRefund));
        }
        $this->setData('max_point_refund', $maxPointRefund);
        return $this->getData('max_point_refund');
    }

    /**
     * get current refund points for this credit memo
     *
     * @return int
     */
    public function getCurrentPoint()
    {
        if (!$this->hasData('max_point_refund')) {
            $this->getMaxPointRefund();
        }
        return (int) $this->getData('current_point');
    }

    /**
     * check admin can refund earned point of customer
     * (deduct point from customer points balance)
     *
     * @return boolean
     */
    public function canRefundEarnedPoints()
    {
        if ($this->getCreditmemo()->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        if ($this->getMaxEarnedRefund()) {
            return true;
        }
        return false;
    }

    /**
     * get max point can deduct from customer balance
     *
     * @return int
     */
    public function getMaxEarnedRefund()
    {
        if (!$this->hasData('max_earned_refund')) {
            $maxEarnedRefund = 0;
            $earnPoint = 0;
            if ($creditmemo = $this->getCreditmemo()) {
                $order = $creditmemo->getOrder();

                $maxEarnedRefund = (int) $this->_transaction->create()->getCollection()
                                ->addFieldToFilter('action', 'earning_invoice')
                                ->addFieldToFilter('order_id', $order->getId())
                                ->getFieldTotal();
                if ($maxEarnedRefund > $order->getSimirewardpointsEarn()) {
                    $maxEarnedRefund = $order->getSimirewardpointsEarn();
                }
                $maxEarnedRefund += (int) $this->_transaction->create()->getCollection()
                                ->addFieldToFilter('action', 'earning_creditmemo')
                                ->addFieldToFilter('order_id', $order->getId())
                                ->getFieldTotal();
                if ($maxEarnedRefund > $order->getSimirewardpointsEarn()) {
                    $maxEarnedRefund = $order->getSimirewardpointsEarn();
                }

                foreach ($creditmemo->getAllItems() as $item) {
                    $orderItem = $item->getOrderItem();
                    if ($orderItem->isDummy()) {
                        continue;
                    }
                    $itemPoint = (int) $orderItem->getSimirewardpointsEarn();
                    $itemPoint = $itemPoint * $item->getQty() / $orderItem->getQtyOrdered();
                    $earnPoint += floor($itemPoint);
                }
                // Hiepdd add shipping earned points
                if ($order->getCreditmemosCollection()->getSize() == 0) {
                    $earnPoint += $this->_helperEarning->getShippingEarningPoints($order);
                }
                
                $maxEarnedRefund = $earnPoint;
            }
            $this->setData('max_earned_refund', $maxEarnedRefund);
        }
        return $this->getData('max_earned_refund');
    }

    public function isLast($creditmemo)
    {
        foreach ($creditmemo->getAllItems() as $item) {
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
