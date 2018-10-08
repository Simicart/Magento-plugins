<?php

/**
 * Simirewardpoints Total Label Block
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Block\Totals\Order;

class Point extends \Magento\Sales\Block\Order\Totals
{

    /**
     * Point constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Simi\Simirewardpoints\Helper\Point $helperPoint
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Simi\Simirewardpoints\Helper\Point $helperPoint,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_helperPoint = $helperPoint;
        parent::__construct($context, $registry, $data);
    }

    /**
     * add points label into order total
     *
     */
    public function initTotals()
    {
        if (!$this->_helperPoint->getGeneralConfig('enable')) {
            return $this;
        }
        $totalsBlock = $this->getParentBlock();
        $order = $totalsBlock->getOrder();

        if ($order->getSimirewardpointsEarn()) {
            $totalsBlock->addTotal(new \Magento\Framework\DataObject([
                'code' => 'simirewardpoints_earn_label',
                'label' => __('Earn Points'),
                'value' => $this->_helperPoint->format($order->getSimirewardpointsEarn()),
                'is_formated' => true,
                    ]), 'subtotal');
        }

        if ($order->getSimirewardpointsSpent()) {
            $totalsBlock->addTotal(new \Magento\Framework\DataObject([
                'code' => 'Simirewardpoints_spent_label',
                'label' => __('Spend Points'),
                'value' => $this->_helperPoint->format($order->getSimirewardpointsSpent()),
                'is_formated' => true,
                    ]), 'Simirewardpoints_earn_label');
        }

        if ($order->getSimirewardpointsDiscount() >= 0.0001) {
            $totalsBlock->addTotal(new \Magento\Framework\DataObject([
                'code' => 'simirewardpoints',
                'label' => __('Use points on spend'),
                'value' => -$order->getSimirewardpointsDiscount(),
                'base_value' => -$order->getSimirewardpointsBaseDiscount(),
                    ]), 'simirewardpoints_spent_label');
        }
    }
}
