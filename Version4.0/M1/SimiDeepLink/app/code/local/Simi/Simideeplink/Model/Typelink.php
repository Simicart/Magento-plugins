<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Simideeplink
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Simideeplink Status Model
 * 
 * @category    Magestore
 * @package     Magestore_Simideeplink
 * @author      Magestore Developer
 */
class Simi_Simideeplink_Model_Typelink extends Varien_Object
{
    const TYPE_NONE    = 0;
    const TYPE_PRODUCT    = 1;
    const TYPE_CATEGORY    = 2;
    const TYPE_CMS   = 3;
    
    /**
     * get model option as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::TYPE_NONE    => Mage::helper('simideeplink')->__('Select Type'),
            self::TYPE_PRODUCT    => Mage::helper('simideeplink')->__('Product'),
            self::TYPE_CATEGORY   => Mage::helper('simideeplink')->__('Category'),
           // self::TYPE_CMS   => Mage::helper('simideeplink')->__('CMS')
        );
    }
    
    /**
     * get model option hash as array
     *
     * @return array
     */
    static public function getOptionHash()
    {
        $options = array();
        foreach (self::getOptionArray() as $value => $label) {
            $options[] = array(
                'value'    => $value,
                'label'    => $label
            );
        }
        return $options;
    }
}