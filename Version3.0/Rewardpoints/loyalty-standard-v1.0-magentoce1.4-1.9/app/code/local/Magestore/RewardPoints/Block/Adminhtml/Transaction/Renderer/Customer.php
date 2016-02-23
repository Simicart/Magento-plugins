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
class Magestore_RewardPoints_Block_Adminhtml_Transaction_Renderer_Customer
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render customer info to grid column html
     * 
     * @param Varien_Object $row
     */
    public function render(Varien_Object $row)
    {
        $actionName = $this->getRequest()->getActionName();
        if (strpos($actionName, 'export') === 0) {
            return $row->getCustomerEmail();
        }
        return sprintf('<a target="_blank" href="%s">%s</a>',
            $this->getUrl('adminhtml/customer/edit', array('id' => $row->getCustomerId())),
            $row->getCustomerEmail()
        );
    }
}
