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
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Order_Sendbackend
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Order_Sendbackend extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract {

    /**
     *
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
    }

    /**
     * @param $prices
     * @return mixed
     * @throws Mage_Core_Exception
     */
    protected function _formatPrices($prices) {
        Mage::app()->getWebsite()->getId();
        $store = Mage::app()->getStore();
        foreach ($prices as $key => $price)
            $prices[$key] = $store->formatPrice($price, false);
        return $prices;
    }

    /**
     * @return int
     */
    public function messageMaxLen() {
        return (int) Mage::helper('simigiftvoucher')->getInterfaceConfig('max');
    }

    /**
     * @return mixed
     */
    public function enablePhysicalMail() {
        return Mage::helper('simigiftvoucher')->getInterfaceConfig('postoffice');
    }

    /**
     * @return Varien_Object
     */
    public function getFormConfigData() {
        $request = Mage::app()->getRequest();
        $action = $request->getRequestedRouteName() . '_' . $request->getRequestedControllerName() . '_' . $request->getRequestedActionName();
        if ($action == 'checkout_cart_configure' && $request->getParam('id')) {
            $request = Mage::app()->getRequest();
            $options = Mage::getModel('sales/quote_item_option')->getCollection()->addItemFilter($request->getParam('id'));
            $formData = array();
            foreach ($options as $option)
                $formData[$option->getCode()] = $option->getValue();
            return new Varien_Object($formData);
        }
        return new Varien_Object();
    }

    /**
     * @return mixed
     */
    public function enableScheduleSend() {
        return Mage::helper('simigiftvoucher')->getInterfaceConfig('schedule');
    }

    /**
     * @return mixed
     */
    public function getGiftAmountDescription() {
        if (!$this->hasData('gift_amount_description')) {
            $product = $this->getProduct();
            $this->setData('gift_amount_description', '');
            if ($product->getShowGiftAmountDesc()) {
                if ($product->getGiftAmountDesc()) {
                    $this->setData('gift_amount_description', $product->getGiftAmountDesc());
                } else {
                    $this->setData('gift_amount_description', Mage::helper('simigiftvoucher')->getInterfaceConfig('description')
                    );
                }
            }
        }
        return $this->getData('gift_amount_description');
    }

    /**
     * 
     * @return type
     */
    public function getAvailableTemplate() {
        $templates = Mage::getModel('simigiftvoucher/gifttemplate')->getCollection()
                ->addFieldToFilter('status', '1');
        return $templates->getData();
    }

    /**
     * @return mixed
     */
    public function getPriceFormatJs() {
        $priceFormat = Mage::app()->getLocale()->getJsPriceFormat();
        return Mage::helper('core')->jsonEncode($priceFormat);
    }

    /**
     * @return bool
     */
    public function isGiftvoucherProduct() {
        $items = Mage::getSingleton('adminhtml/session_quote')->getQuote()->getAllItems();
        foreach ($items as $item) {
            if ($item->getProductType() == 'simigiftvoucher') {
                return true;
            }
        }
        return false;
    }

}
