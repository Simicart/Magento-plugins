<?php

/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Paytabsmobile
 * @copyright   Copyright (c) 2012
 * @license     
 */

/**
 * Paytabsmobile Model
 * 
 * @category    
 * @package     Paytabsmobile
 * @author      Developer
 */
class Simi_Paytabsmobile_Model_Status extends Varien_Object {

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    /**
     * get model option as array
     *
     * @return array
     */
    static public function getOptionArray() {
        return array(
            self::STATUS_ENABLED => Mage::helper('paytabsmobile')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('paytabsmobile')->__('Disabled')
        );
    }

    /**
     * get model option hash as array
     *
     * @return array
     */
    static public function getOptionHash() {
        $options = array();
        foreach (self::getOptionArray() as $value => $label)
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        return $options;
    }

}
