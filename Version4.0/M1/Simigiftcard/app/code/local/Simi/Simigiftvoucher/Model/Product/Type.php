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
 * Giftvoucher Product Type Model
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */
class Simi_Simigiftvoucher_Model_Product_Type extends Mage_Catalog_Model_Product_Type_Abstract
{

    protected $_canConfigure = true;

    /**
     * Initialize product(s) for add to cart process
     *
     * @param Varien_Object $buyRequest
     * @param Mage_Catalog_Model_Product $product
     * @return array|string
     */
    public function prepareForCart(Varien_Object $buyRequest, $product = null)
    {
        if (version_compare(Mage::getVersion(), '1.5.0', '>=')) {
            return parent::prepareForCart($buyRequest, $product);
        }
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        $result = parent::prepareForCart($buyRequest, $product);
        if (is_string($result)) {
            return $result;
        }
        reset($result);
        $product = current($result);
        $result = $this->_prepareGiftVoucher($buyRequest, $product);
        return $result;
    }

    /**
     * Prepare product and its configuration to be added to some products list.
     * Perform standard preparation process and then prepare options belonging to specific product type.
     *
     * @param  Varien_Object $buyRequest
     * @param  Mage_Catalog_Model_Product $product
     * @param  string $processMode
     * @return array|string
     */
    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
    {
        if (version_compare(Mage::getVersion(), '1.5.0', '<')) {
            return parent::_prepareProduct($buyRequest, $product, $processMode);
        }
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        if (!$buyRequest->getData('send_friend')) {
            $fields = array('recipient_name', 'recipient_email', 'message', 'day_to_send', 'timezone_to_send', 
                'recipient_address', 'notify_success');
            foreach ($fields as $field) {
                if ($buyRequest->getData($field)) {
                    $buyRequest->unsetData($field);
                }
            }
        }
        $result = parent::_prepareProduct($buyRequest, $product, $processMode);
        if (is_string($result)) {
            return $result;
        }
        reset($result);
        $product = current($result);
        $result = $this->_prepareGiftVoucher($buyRequest, $product);
        return $result;
    }

    /**
     * Prepare Gift Card product
     *
     * @param  Varien_Object $buyRequest
     * @param  Mage_Catalog_Model_Product $product
     * @return array|string
     */
    protected function _prepareGiftVoucher(Varien_Object $buyRequest, $product)
    {
        if (Mage::app()->getStore()->isAdmin()) {
            $store = Mage::getSingleton('adminhtml/session_quote')->getStore();
        } else {
            $store = Mage::app()->getStore();
        }

        $amount = $buyRequest->getAmount();
        $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
        $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
        $baseCurrency = Mage::getModel('directory/currency')->load($baseCurrencyCode);
        $currentCurrency = Mage::getModel('directory/currency')->load($currentCurrencyCode);
        $baseAmount = $baseCurrency->convert($amount, $currentCurrency);
        $baseValue = $amount;
        $fnPrice = 0;
        if ($amount) {
            $giftAmount = Mage::helper('simigiftvoucher/giftproduct')->getGiftValue($product);
            //echo json_encode($giftAmount);die('xx');
            switch ($giftAmount['type_value']) {
                case 'range':
                    if ($amount < $this->convertPrice($product, $giftAmount['from'])) {
                        $amount = $this->convertPrice($product, $giftAmount['from']);
                        $baseValue = $giftAmount['from'];
                    } elseif ($amount > $this->convertPrice($product, $giftAmount['to'])) {
                        $amount = $this->convertPrice($product, $giftAmount['to']);
                        $baseValue = $giftAmount['to'];
                    } else {
                        $baseCurrencyCode = $store->getBaseCurrencyCode();
                        $currentCurrencyCode = $store->getCurrentCurrencyCode();

                        $baseCurrency = Mage::getModel('directory/currency')->load($baseCurrencyCode);
                        $currentCurrency = Mage::getModel('directory/currency')->load($currentCurrencyCode);

                        // convert price from current currency to base currency
                        if ($amount > 0) {
                            $amount = $amount * $amount / $baseCurrency->convert($amount, $currentCurrency);
                            $baseValue = $amount;
                        } else {
                            $amount = 0;
                            $baseValue = 0;
                        }
                    }

                    $fnPrice = $amount;
                    if ($giftAmount['type_price'] == 'percent') {
                        $fnPrice = $fnPrice * $giftAmount['percent_value'] / 100;
                    }
                    break;
                case 'dropdown':
                    if (!empty($giftAmount['options_value'])) {
                        $check = false;
                        $giftDropdown = array();
                        for ($i = 0; $i < count($giftAmount['options_value']); $i++) {
                            $giftDropdown[$i] = $this->convertPrice($product, $giftAmount['options_value'][$i]);
                            if ($amount == $giftDropdown[$i]) {
                                $check = true;
                                $baseValue = $giftAmount['options_value'][$i];
                            }
                        }
                        if (!$check) {
                            $amount = $giftAmount['options_value'][0];
                            $baseValue = $giftAmount['options_value'][0];
                        }

                        $fnPrices = array_combine($giftDropdown, $giftAmount['prices_dropdown']);
                        $fnPrice = $fnPrices[$amount];
                    }
                    break;
                case 'fixed':
                    if ($amount != $this->convertPrice($product, $giftAmount['value'])) {
                        $amount = $giftAmount['value'];
                    }
                    $baseValue = $giftAmount['value'];
                    $fnPrice = $giftAmount['price'];
                    break;
                default:
                    return Mage::helper('simigiftvoucher')->__('Please enter Gift Card information.');
            }
        } else {
            return Mage::helper('simigiftvoucher')->__('Please enter Gift Card information.');
        }

        $buyRequest->setAmount($amount);
        $product->addCustomOption('base_gc_value', $baseValue);
        $product->addCustomOption('base_gc_currency', Mage::app()->getStore()->getBaseCurrencyCode());
        $product->addCustomOption('gc_product_type', $giftAmount['type_value']);
        $product->addCustomOption('price_amount', $fnPrice);

        foreach (Mage::helper('simigiftvoucher')->getFullGiftVoucherOptions() as $key => $label) {
            if ($value = $buyRequest->getData($key)) {
                $product->addCustomOption($key, $value);
            }
        }
        if (!Mage::registry('haitv_product_' . $product->getId())) {
            Mage::register('haitv_product_' . $product->getId(), $product);
        }
        return array($product);
    }

    /**
     * Check is virtual product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isVirtual($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        if (!Mage::helper('simigiftvoucher')->getInterfaceConfig('postoffice', $product->getStoreId())) {
            return true;
        }

        $productOption = $this->getProduct($product)->getCustomOption('recipient_ship');
        if (!$productOption) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if product has required options
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function hasRequiredOptions($product = null)
    {
        return true;
    }

    /**
     * Check if product is configurable
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function canConfigure($product = null)
    {
        return TRUE;
    }

    /**
     * Convert the price of product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float $price
     * @return string
     */
    public function convertPrice($product, $price)
    {
        $includeTax = ( Mage::getStoreConfig('tax/display/type') != 1 );
        if (Mage::app()->getStore()->isAdmin()) {
            $store = Mage::getSingleton('adminhtml/session_quote')->getStore();
        } else {
            $store = Mage::app()->getStore();
        }

        $priceWithTax = Mage::helper('tax')->getPrice($product, $price, $includeTax);
        return $store->convertPrice($priceWithTax);
    }

}
