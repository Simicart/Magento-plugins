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
class Magestore_RewardPoints_Model_System_Config_Source_Rounding
{
    /**
     * Options getter
     * 
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'round', 'label' => Mage::helper('rewardpoints')->__('Normal')),
            array('value' => 'floor', 'label' => Mage::helper('rewardpoints')->__('Rounding Down')),
            array('value' => 'ceil', 'label' => Mage::helper('rewardpoints')->__('Rounding Up')),
        );
    }
}
