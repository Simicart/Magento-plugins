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
class Magestore_RewardPoints_Helper_Policy extends Mage_Core_Helper_Abstract
{
    const XML_PATH_SHOW_POLICY  = 'rewardpoints/general/show_policy_menu';
    const XML_PATH_POLICY_PAGE  = 'rewardpoints/general/policy_page';
    
    /**
     * get Policy URL, return the url to view Policy
     * 
     * @return string
     */
    public function getPolicyUrl()
    {
        if (!Mage::getStoreConfigFlag(self::XML_PATH_SHOW_POLICY)) {
            return Mage::getUrl('rewardpoints/index/index');
        }
        return Mage::getUrl('rewardpoints/index/policy');
    }
    
    /**
     * Check policy menu configuration
     * 
     * @param mixed $store
     * @return boolean
     */
    public function showPolicyMenu($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SHOW_POLICY, $store);
    }
}
