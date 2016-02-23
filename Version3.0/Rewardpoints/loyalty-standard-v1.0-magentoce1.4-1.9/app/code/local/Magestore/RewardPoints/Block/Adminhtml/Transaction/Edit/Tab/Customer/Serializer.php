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
class Magestore_RewardPoints_Block_Adminhtml_Transaction_Edit_Tab_Customer_Serializer
    extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('rewardpoints/transaction/customer/serializer.phtml');
        return $this;
    }
    
    /**
     * init serializer block, called from layout
     * 
     * @param string $gridName
     * @param string $hiddenInputName
     */
    public function initSerializerBlock($gridName, $hiddenInputName)
    {
        $grid = $this->getLayout()->getBlock($gridName);
        $this->setGridBlock($grid)
            ->setInputElementName($hiddenInputName);
    }
}
