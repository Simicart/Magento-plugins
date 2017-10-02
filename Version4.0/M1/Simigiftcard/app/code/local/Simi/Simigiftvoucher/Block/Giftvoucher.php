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
 * Giftvoucher block
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */
class Simi_Simigiftvoucher_Block_Giftvoucher extends Mage_Core_Block_Template
{

    /**
     * @return Mage_Core_Block_Abstract
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('simigiftvoucher/index/check');
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return Mage::app()->getRequest()->getParam('code', null);
    }

    /**
     * @return mixed
     */
    public function getCodeTxt()
    {
        return Mage::helper('simigiftvoucher')->getHiddenCode($this->getCode());
    }

    /**
     * @return null
     */
    public function getGiftVoucher()
    {
        if ($code = $this->getCode()) {
            $codes = Mage::getSingleton('simigiftvoucher/session')->getCodes();
            $codes[] = $code;
            $codes = array_unique($codes);
            if ($max = Mage::helper('simigiftvoucher')->getGeneralConfig('maximum')) {
                if (count($codes) > $max) {
                    return null;
                }
            }

            Mage::getSingleton('simigiftvoucher/session')->setCodes($codes);
            $giftVoucher = Mage::getModel('simigiftvoucher/giftvoucher')->loadByCode($code);
            if ($giftVoucher->getId()) {
                return $giftVoucher;
            }
        }
        return null;
    }

    /**
     * Returns the formatted balance
     * 
     * @param Simi_Simigiftvoucher_Model_Giftvoucher $giftVoucher
     * @return string
     */
    public function getBalanceFormat($giftVoucher)
    {
        $currency = Mage::getModel('directory/currency')->load($giftVoucher->getCurrency());
        return $currency->format($giftVoucher->getBalance());
    }

    /**
     * Get status of gift code
     * 
     * @param Simi_Simigiftvoucher_Model_Giftvoucher $giftVoucher
     * @return string
     */
    public function getStatus($giftVoucher)
    {
        $status = $giftVoucher->getStatus();
        $statusArray = Mage::getSingleton('simigiftvoucher/status')->getOptionArray();
        return $statusArray[$status];
    }

}
