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
class Magestore_RewardPoints_Block_Name extends Magestore_RewardPoints_Block_Template
{
    /**
     * prepare block's layout
     *
     * @return Magestore_RewardPoints_Block_Name
     */
    public function _prepareLayout()
    {
        $this->setTemplate('rewardpoints/name.phtml');
        return parent::_prepareLayout();
    }
    
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
     * get Image (Logo) HTML for reward points
     * 
     * @return string
     */
    public function getImageHtml()
    {
        return Mage::helper('rewardpoints/point')->getImageHtml($this->getIsAnchorMode());
    }
}
