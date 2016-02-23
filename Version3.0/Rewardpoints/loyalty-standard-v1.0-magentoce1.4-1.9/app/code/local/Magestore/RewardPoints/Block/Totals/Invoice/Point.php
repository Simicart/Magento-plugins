<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Rewardpoints
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Rewardpoints Block
 * 
 * @category    
 * @package     Rewardpoints
 * @author      Developer
 */
class Magestore_RewardPoints_Block_Totals_Invoice_Point extends Magestore_RewardPoints_Block_Template
{
    /**
     * add points value into invoice total
     *     
     */
    public function initTotals()
    {
        if (!$this->isEnable()) {
            return $this;
        }
        $totalsBlock = $this->getParentBlock();
        $invoice = $totalsBlock->getInvoice();
        if ($invoice->getRewardpointsEarn()) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code' => 'rewardpoints_earn_label',
                'label' => $this->__('Earn Points'),
                'value' => Mage::helper('rewardpoints/point')->format($invoice->getRewardpointsEarn()),
                'is_formated' => true,
                    )), 'subtotal');
        }
        if ($invoice->getRewardpointsDiscount()) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code'  => 'rewardpoints',
                'label' => $this->__('Use points on spend'),
                'value' => -$invoice->getRewardpointsDiscount(),
            )), 'subtotal');
        }
    }
}
