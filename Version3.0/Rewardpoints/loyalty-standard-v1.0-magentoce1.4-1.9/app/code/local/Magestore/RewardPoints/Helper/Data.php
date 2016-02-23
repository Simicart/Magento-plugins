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
 * Rewardpoints Helper
 * 
 * @category    
 * @package     Rewardpoints
 * @author      Developer
 */
class Magestore_RewardPoints_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLE = 'rewardpoints/general/enable';
    
    /**
     * check reward points system is enabled
     * 
     * @param mixed $store
     * @return boolean
     */
    public function isEnable($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLE, $store);
    }
    public function isEnableOutput(){
        return Mage::helper('core')->isModuleOutputEnabled('Magestore_RewardPoints');
    }
    
    /**
     * get rewards points label to show on Account Navigation
     * 
     * @return string
     */
    public function getMyRewardsLabel()
    {
        $pointAmount = Mage::helper('rewardpoints/customer')->getBalance();
        if ($pointAmount > 0) {
            $rate = Mage::getModel('rewardpoints/rate')->getRate(Magestore_RewardPoints_Model_Rate::POINT_TO_MONEY);
            if ($rate && $rate->getId()) {
                $baseAmount = $pointAmount * $rate->getMoney() / $rate->getPoints();
                $pointAmount = Mage::app()->getStore()->convertPrice($baseAmount, true);
            }
        }
        $imageHtml = Mage::helper('rewardpoints/point')->getImageHtml(false);
        return $this->__('My Rewards') . ' ' . $pointAmount . $imageHtml;
    }
    
}
