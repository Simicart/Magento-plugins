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
 * Giftvoucher Pdf Shipment Item Model
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */

class Simi_Simigiftvoucher_Model_Pdf_Shipment_Item extends Mage_Sales_Model_Order_Pdf_Items_Shipment_Default
{

    /**
     * Retrieve item options
     *
     * @return array
     */
    public function getItemOptions()
    {
        $result = parent::getItemOptions();
        $item = Mage::getModel('sales/order_item')->load($this->getItem()->getOrderItemId());

        if ($item->getProductType() != 'simigiftvoucher') {
            return $result;
        }

        if ($options = $item->getProductOptionByCode('info_buyRequest')) {
            foreach (Mage::helper('simigiftvoucher')->getGiftVoucherOptions() as $code => $label) {
                if ($options[$code]) {
                    if ($code == 'giftcard_template_id') {
                        $valueTemplate = Mage::getModel('simigiftvoucher/gifttemplate')->load($options[$code]);
                        $result[] = array(
                            'label' => $label,
                            'value' => Mage::helper('core')->escapeHtml($valueTemplate->getTemplateName()),
                            'option_value' => Mage::helper('core')->escapeHtml($valueTemplate->getTemplateName()),
                        );
                    } else {
                        $result[] = array(
                            'label' => $label,
                            'value' => $options[$code],
                            'print_value' => $options[$code],
                        );
                    }
                }
            }
        }

        $giftVouchers = Mage::getModel('simigiftvoucher/giftvoucher')->getCollection()->addItemFilter($item->getId());
        if ($giftVouchers->getSize()) {
            $giftVouchersCode = array();
            foreach ($giftVouchers as $giftVoucher) {
                $currency = Mage::getModel('directory/currency')->load($giftVoucher->getCurrency());
                $balance = $giftVoucher->getBalance();
                if ($currency) {
                    $balance = $currency->format($balance, array(), false);
                }
                $giftVouchersCode[] = $giftVoucher->getGiftCode() . ' (' . $balance . ') ';
            }
            $codes = implode(' ', $giftVouchersCode);
            $result[] = array(
                'label' => Mage::helper('simigiftvoucher')->__('Gift Card Code'),
                'value' => $codes,
                'print_value' => $codes,
            );
        }

        return $result;
    }

}
