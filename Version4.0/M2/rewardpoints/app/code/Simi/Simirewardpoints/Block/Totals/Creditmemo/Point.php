<?php

/**
 * Simirewardpoints Total Label Block
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Block\Totals\Creditmemo;

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
     * add points value into creditmemo total
     *
     */
    public function initTotals()
    {
        if (!$this->_helperPoint->getGeneralConfig('enable')) {
            return $this;
        }
        $totalsBlock = $this->getParentBlock();
        $creditmemo = $totalsBlock->getCreditmemo();

        if ($creditmemo->getSimiRewardpointsEarn()) {
            $totalsBlock->addTotal(new \Magento\Framework\DataObject([
                'code' => 'simirewardpoints_earn_label',
                'label' => __('Earn Points'),
                'value' => $this->_helperPoint->format($creditmemo->getSimiRewardpointsEarn()),
                'is_formated' => true,
                    ]), 'subtotal');
        }

        if ($creditmemo->getSimiRewardpointsSpent()) {
            $totalsBlock->addTotal(new \Magento\Framework\DataObject([
                'code' => 'simirewardpoints_spent_label',
                'label' => __('Spend Points'),
                'value' => $this->_helperPoint->format($creditmemo->getSimiRewardpointsSpent()),
                'is_formated' => true,
                    ]), 'simirewardpoints_earn_label');
        }

        if ($creditmemo->getSimiRewardpointsDiscount() >= 0.0001) {
            $totalsBlock->addTotal(new \Magento\Framework\DataObject([
                'code' => 'simirewardpoints',
                'label' => __('Use points on spend'),
                'value' => -$creditmemo->getSimiRewardpointsDiscount(),
                'base_value' => -$creditmemo->getSimiRewardpointsBaseDiscount(),
                    ]), 'simirewardpoints_spent_label');
        }
    }
}
