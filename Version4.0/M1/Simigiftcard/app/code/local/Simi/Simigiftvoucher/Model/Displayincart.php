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
 * Giftvoucher Displayincart Model
 * 
 * @category    Simi
 * @package     Simi_Simigiftvoucher
 * @author      Simi Developer
 */
class Simi_Simigiftvoucher_Model_Displayincart
{

    /**
     * Get model option as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $positions = array(
            'amount' => Mage::helper('simigiftvoucher')->__('Gift Card value'),
            'giftcard_template_id' => Mage::helper('simigiftvoucher')->__('Gift Card template'),
            'customer_name' => Mage::helper('simigiftvoucher')->__('Sender name'),
            'recipient_name' => Mage::helper('simigiftvoucher')->__('Recipient name'),
            'recipient_email' => Mage::helper('simigiftvoucher')->__('Recipient email address'),
            'recipient_ship' => Mage::helper('simigiftvoucher')->__('Ship to recipient'),
            'recipient_address' => Mage::helper('simigiftvoucher')->__('Recipient address'),
            'message' => Mage::helper('simigiftvoucher')->__('Custom message'),
            'day_to_send' => Mage::helper('simigiftvoucher')->__('Day to send'),
            'timezone_to_send' => Mage::helper('simigiftvoucher')->__('Time zone'),
        );
        $options = array();

        foreach ($positions as $code => $label) {
            $options[] = array(
                'value' => $code,
                'label' => $label
            );
        }
        return $options;
    }

}
