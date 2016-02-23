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
class Magestore_RewardPoints_Block_Adminhtml_Spending_Renderer_Money
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if ($row->getDirection() == Magestore_RewardPoints_Model_Rate::POINT_TO_MONEY) {
            return Mage::app()->getStore()->getBaseCurrency()->format($row->getMoney());
        } else {
            $result = new Varien_Object(array(
                'value' => ''
            ));
            Mage::dispatchEvent('rewardpoints_adminhtml_spending_rate_grid_renderer', array(
                'row'   => $row,
                'result'=> $result
            ));
            return $result->getData('value');
        }
    }
}
