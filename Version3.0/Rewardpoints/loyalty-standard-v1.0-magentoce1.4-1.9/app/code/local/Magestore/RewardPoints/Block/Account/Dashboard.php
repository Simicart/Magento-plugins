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
class Magestore_RewardPoints_Block_Account_Dashboard extends Magestore_RewardPoints_Block_Template
{
    /**
     * get current balance of customer as text
     * 
     * @return string
     */
    public function getBalanceText()
    {
        return Mage::helper('rewardpoints/customer')->getBalanceFormated();
    }
    
    /**
     * get holding balance of customer as text
     * 
     * @return int
     */
    public function getHoldingBalance()
    {
        $holdingBalance = Mage::helper('rewardpoints/customer')->getAccount()->getHoldingBalance();
        if ($holdingBalance > 0) {
            return Mage::helper('rewardpoints/point')->format($holdingBalance);
        }
        return '';
    }
    
    /**
     * get point money balance of customer
     * 
     * @return string
     */
    public function getPointMoney()
    {
        $pointAmount = Mage::helper('rewardpoints/customer')->getBalance();
        if ($pointAmount > 0) {
            $rate = Mage::getModel('rewardpoints/rate')->getRate(Magestore_RewardPoints_Model_Rate::POINT_TO_MONEY);
            if ($rate && $rate->getId()) {
                $baseAmount = $pointAmount * $rate->getMoney() / $rate->getPoints();
                return Mage::app()->getStore()->convertPrice($baseAmount, true);
            }
        }
        return '';
    }
}
