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
 * Giftvoucher Status Model
 * 
 * @category    Simi
 * @package     Simi_Simigiftvoucher
 * @author      Simi Developer
 */
class Simi_Simigiftvoucher_Model_Status extends Varien_Object
{

    const STATUS_PENDING = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_DISABLED = 3;
    const STATUS_USED = 4;
    const STATUS_EXPIRED = 5;
    const STATUS_DELETED = 6;
    const STATUS_NOT_SEND = 0;
    const STATUS_SENT_EMAIL = 1;
    const STATUS_SENT_OFFICE = 2;

    /**
     * Get the gift code's status options as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::STATUS_PENDING => Mage::helper('simigiftvoucher')->__('Pending'),
            self::STATUS_ACTIVE => Mage::helper('simigiftvoucher')->__('Active'),
            self::STATUS_DISABLED => Mage::helper('simigiftvoucher')->__('Disabled'),
            self::STATUS_USED => Mage::helper('simigiftvoucher')->__('Used'),
            self::STATUS_EXPIRED => Mage::helper('simigiftvoucher')->__('Expired'),
        );
    }

    /**
     * Get the email's status options as array 
     *
     * @return array
     */
    static public function getOptionEmail()
    {
        return array(
            self::STATUS_NOT_SEND => Mage::helper('simigiftvoucher')->__('Not Send'),
            self::STATUS_SENT_EMAIL => Mage::helper('simigiftvoucher')->__('Sent via Email'),
            self::STATUS_SENT_OFFICE => Mage::helper('simigiftvoucher')->__('Send via Post Office'),
        );
    }

    /**
     * Get the gift code's status options  
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

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return self::getOptions();
    }

}
