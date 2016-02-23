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
class Magestore_RewardPoints_Block_Totals_Creditmemo_Point extends Magestore_RewardPoints_Block_Template
{
    /**
     * add points value into creditmemo total
     *     
     */
    public function initTotals()
    {
        if (!$this->isEnable()) {
            return $this;
        }
        $totalsBlock = $this->getParentBlock();
        $creditmemo = $totalsBlock->getCreditmemo();
        if ($creditmemo->getRewardpointsEarn()) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code'  => 'rewardpoints_earn_label',
                'label' => $this->__('Refund Points Earn'),
                'value' => $creditmemo->getRewardpointsEarn(),
                'is_formated'   => true,
            )), 'subtotal');
        }
        if ($creditmemo->getRewardpointsDiscount()) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code'  => 'rewardpoints',
                'label' => $this->__('Use points on spend'),
                'value' => -$creditmemo->getRewardpointsDiscount(),
            )), 'subtotal');
        }
    }
}
