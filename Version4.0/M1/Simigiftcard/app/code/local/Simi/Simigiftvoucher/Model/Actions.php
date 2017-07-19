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
 * Giftvoucher Actions Model
 * 
 * @category    Simi
 * @package     Simi_Simigiftvoucher
 * @author      Simi Developer
 */
class Simi_Simigiftvoucher_Model_Actions extends Varien_Object
{

    const ACTIONS_CREATE = 1;
    const ACTIONS_UPDATE = 2;
    const ACTIONS_MASS_UPDATE = 3;
    const ACTIONS_EMAIL = 4;
    const ACTIONS_SPEND_ORDER = 5;
    const ACTIONS_REFUND = 6;
    const ACTIONS_REDEEM = 7;

    /**
     * Get model option as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::ACTIONS_CREATE => Mage::helper('simigiftvoucher')->__('Create'),
            self::ACTIONS_UPDATE => Mage::helper('simigiftvoucher')->__('Update'),
            self::ACTIONS_MASS_UPDATE => Mage::helper('simigiftvoucher')->__('Mass update'),
            self::ACTIONS_SPEND_ORDER => Mage::helper('simigiftvoucher')->__('Spent on order'),
            self::ACTIONS_REFUND => Mage::helper('simigiftvoucher')->__('Refund'),
            self::ACTIONS_REDEEM => Mage::helper('simigiftvoucher')->__('Redeem'),
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

}
