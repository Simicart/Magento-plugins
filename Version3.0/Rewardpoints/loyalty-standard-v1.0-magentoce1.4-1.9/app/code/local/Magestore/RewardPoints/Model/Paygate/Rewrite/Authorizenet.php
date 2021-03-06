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
 * Rewardpoints Model
 * 
 * @category    
 * @package     Rewardpoints
 * @author      Developer
 */
class Magestore_RewardPoints_Model_Paygate_Rewrite_Authorizenet extends Mage_Paygate_Model_Authorizenet
{
    /**
     * Send request with new payment to gateway
     *
     * @param Mage_Payment_Model_Info $payment
     * @param decimal $amount
     * @param string $requestType
     * @return Mage_Paygate_Model_Authorizenet
     * @throws Mage_Core_Exception
     */
    protected function _place($payment, $amount, $requestType)
    {
        /** @var $helper Magestore_RewardPoints_Helper_Calculation_Spending */
        $helper = Mage::helper('rewardpoints/calculation_spending');
        
        $rewardPointsDiscount  = $helper->getPointItemDiscount();
        $rewardPointsDiscount += $helper->getCheckedRuleDiscount();
        $rewardPointsDiscount += $helper->getSliderRuleDiscount();
        
        $container = new Varien_Object(array(
            'reward_points_discount' => $rewardPointsDiscount
        ));
        Mage::dispatchEvent('rewardpoints_rewrite_authorizenet_place', array(
            'container' => $container
        ));
        
        if ($container->getRewardPointsDiscount() > 0 && $requestType == self::REQUEST_TYPE_AUTH_ONLY) {
            $amount -= Mage::app()->getStore()->convertPrice($container->getRewardPointsDiscount());
        }
        
        return parent::_place($payment, $amount, $requestType);
    }
}
