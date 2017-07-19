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
 * Giftvoucher Creditaction Model
 * 
 * @category    Simi
 * @package     Simi_Simigiftvoucher
 * @author      Simi Developer
 */

class Simi_Simigiftvoucher_Model_Creditaction extends Varien_Object
{

    const ACTIONS_REDEEM = 'Redeem';
    const ACTIONS_APIREDEEM = 'Api_re';
    const ACTIONS_APIUPDATE = 'Apiupdate';
    const ACTIONS_ADMINUPDATE = 'Adminupdate';
    const ACTIONS_SPEND = 'Spend';
    const ACTIONS_REFUND = 'Refund';

    /**
     * Get model option as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::ACTIONS_REDEEM => Mage::helper('simigiftvoucher')->__('Customer Redemption'),
            self::ACTIONS_APIREDEEM => Mage::helper('simigiftvoucher')->__('API User Redemption'),
            self::ACTIONS_APIUPDATE => Mage::helper('simigiftvoucher')->__('API User Update'),
            self::ACTIONS_ADMINUPDATE => Mage::helper('simigiftvoucher')->__('Admin Update'),
            self::ACTIONS_SPEND => Mage::helper('simigiftvoucher')->__('Customer Spend'),
            self::ACTIONS_REFUND => Mage::helper('simigiftvoucher')->__('Admin Refund'),
        );
    }

    /**
     * Get all options
     *
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
