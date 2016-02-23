<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Hideaddress
 * @copyright   Copyright (c) 2012 
 * @license   
 */

/**
 * Hideaddress Model
 * 
 * @category    
 * @package     Hideaddress
 * @author      Developer
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
class Simi_Hideaddress_Model_Options_Options
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Required')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('Optional')),
            array('value' => 3, 'label'=>Mage::helper('adminhtml')->__('Hide')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            1 => Mage::helper('adminhtml')->__('Required'),
            2 => Mage::helper('adminhtml')->__('Optional'),
            3 => Mage::helper('adminhtml')->__('Hide'),          
        );
    }

}
