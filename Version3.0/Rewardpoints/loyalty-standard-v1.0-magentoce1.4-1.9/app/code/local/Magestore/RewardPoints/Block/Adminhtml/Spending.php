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
class Magestore_RewardPoints_Block_Adminhtml_Spending extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_spending';
        $this->_blockGroup = 'rewardpoints';
        $this->_headerText = Mage::helper('rewardpoints')->__('Spending Rates');
        $this->_addButtonLabel = Mage::helper('rewardpoints')->__('Add Rate');
        parent::__construct();
    }
}
