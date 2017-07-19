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
 * Giftvoucher Validateconfig Model
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */

class Simi_Simigiftvoucher_Model_Validateconfig extends Mage_Core_Model_Config_Data
{

    /**
     * @return Mage_Core_Model_Abstract
     * @throws Exception
     */
    public function save()
    {
        $max = $this->getValue();
        if ($max > 240) {
            $this->setValue(240);
            Mage::getSingleton('adminhtml/session')
                ->addNotice('Message max length cannot be greater than 240 characters.');
        }
        return parent::save();
    }

}
