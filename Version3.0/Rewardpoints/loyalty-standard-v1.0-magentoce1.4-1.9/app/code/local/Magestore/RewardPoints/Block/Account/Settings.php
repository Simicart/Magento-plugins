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
class Magestore_RewardPoints_Block_Account_settings extends Magestore_RewardPoints_Block_Template
{
    /**
     * get current reward points account
     * 
     * @return Magestore_RewardPoints_Model_Customer
     */
    public function getRewardAccount()
    {
        $rewardAccount = Mage::helper('rewardpoints/customer')->getAccount();
        if (!$rewardAccount->getId()) {
            $rewardAccount->setIsNotification(1)
                ->setExpireNotification(1);
        }
        return $rewardAccount;
    }
}
