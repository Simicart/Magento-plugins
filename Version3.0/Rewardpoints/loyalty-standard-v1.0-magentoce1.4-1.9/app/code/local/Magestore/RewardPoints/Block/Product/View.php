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
class Magestore_RewardPoints_Block_Product_View extends Magestore_RewardPoints_Block_Template
{
    /**
     * prepare product info.extrahint block information
     *
     * @return Magestore_RewardPoints_Block_Template
     */
    public function _prepareLayout()
    {
        if ($this->isEnable() && version_compare(Mage::getVersion(), '1.4.1.0', '<')) {
            $productInfo = $this->getLayout()->getBlock('product.info');
            $productInfo->setTemplate('rewardpoints/product/view.phtml');
            $extrahints = $this->getLayout()->createBlock('core/text_list', 'product.info.extrahint');
            $productInfo->setChild('extrahint', $extrahints);
        }
        return parent::_prepareLayout();
    }
}
