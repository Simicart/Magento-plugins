<?php
/**
 * Simi
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Simicart.com license that is
 * available through the world-wide-web at this URL:
 * http://www.Simicart.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Simi
 * @package     Simi_Simigiftvoucher
 * @module     Giftvoucher
 * @author      Simi Developer
 *
 * @copyright   Copyright (c) 2016 Simi (http://www.Simicart.com/)
 * @license     http://www.Simicart.com/license-agreement.html
 *
 */

/**
 * Giftvoucher Designpattern Model
 * 
 * @category    Simi
 * @package     Simi_Simigiftvoucher
 * @author      Simi Developer
 */

class Simi_Simigiftvoucher_Model_Designpattern extends Varien_Object
{

    const PATTERN_LEFT = 1;
    const PATTERN_TOP = 2;
    const PATTERN_CENTER = 3;
    const PATTERN_SIMPLE = 4;
    const PATTERN_AMAZON = 5;

    /**
     * Get model option as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::PATTERN_LEFT => Mage::helper('simigiftvoucher')->__('Left'),
            self::PATTERN_TOP => Mage::helper('simigiftvoucher')->__('Top'),
            self::PATTERN_CENTER => Mage::helper('simigiftvoucher')->__('Center'),
            self::PATTERN_SIMPLE => Mage::helper('simigiftvoucher')->__('Simple'),
            self::PATTERN_AMAZON => Mage::helper('simigiftvoucher')->__('Amazon Gift Card Style')
        );
    }

    /**
     * @return array
     */
    static public function getOptions()
    {
        $options = array();
        foreach (self::getOptionArray() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }
        return $options;
    }

    /**
     * @return array
     */
    static public function getOnlyNewTemplate()
    {
        return array(
            array(
                'value' => self::PATTERN_AMAZON,
                'label' => Mage::helper('simigiftvoucher')->__('Amazon Gift Card Style')
            )
        );
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return self::getOptions();
    }

}
