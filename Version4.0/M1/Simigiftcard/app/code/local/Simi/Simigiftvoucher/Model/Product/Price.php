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
 * Giftvoucher Product Price Model
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */
class Simi_Simigiftvoucher_Model_Product_Price extends Mage_Catalog_Model_Product_Type_Price
{

    const PRICE_TYPE_FIXED = 1;
    const PRICE_TYPE_DYNAMIC = 0;

    /**
     * Get Gift Card price information
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getGiftAmount($product = null)
    {

        $giftAmount = Mage::helper('simigiftvoucher/giftproduct')->getGiftValue($product);

        switch ($giftAmount['type_value']) {
            case 'range':
                $giftAmount['min_price'] = $giftAmount['from'];
                $giftAmount['max_price'] = $giftAmount['to'];
                $giftAmount['price'] = $giftAmount['from'];
                if ($giftAmount['type_price'] == 'percent') {
                    $giftAmount['price'] = $giftAmount['price'] * $giftAmount['percent_value'] / 100;
                    $giftAmount['min_price'] = $giftAmount['from'] * $giftAmount['percent_value'] / 100;
                    $giftAmount['max_price'] = $giftAmount['to'] * $giftAmount['percent_value'] / 100;
                }

                if ($giftAmount['min_price'] == $giftAmount['max_price']) {
                    $giftAmount['price_type'] = self::PRICE_TYPE_FIXED;
                } else {
                    $giftAmount['price_type'] = self::PRICE_TYPE_DYNAMIC;
                }
                break;
            case 'dropdown':
                $giftAmount['min_price'] = min($giftAmount['prices_dropdown']);
                $giftAmount['max_price'] = max($giftAmount['prices_dropdown']);
                $giftAmount['price'] = $giftAmount['prices_dropdown'][0];
                if ($giftAmount['min_price'] == $giftAmount['max_price']) {
                    $giftAmount['price_type'] = self::PRICE_TYPE_FIXED;
                } else {
                    $giftAmount['price_type'] = self::PRICE_TYPE_DYNAMIC;
                }
                break;
            case 'fixed':
                //$giftAmount['price'] = $giftAmount['price'];
                $giftAmount['price_type'] = self::PRICE_TYPE_FIXED;
                break;
            default:
                $giftAmount['min_price'] = 0;
                $giftAmount['price_type'] = self::PRICE_TYPE_DYNAMIC;
                $giftAmount['price'] = 0;
        }
        //zend_debug::dump($giftAmount);
        return $giftAmount;
    }

    /**
     * Default action to get price of product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return decimal
     */
    public function getPrice($product)
    {
        $giftAmount = $this->getGiftAmount($product);
        return $giftAmount['price'];
    }

    /**
     * Apply options price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $qty
     * @param float $finalPrice
     * @return float
     */
    protected function _applyOptionsPrice($product, $qty, $finalPrice)
    {
        if ($amount = $product->getCustomOption('price_amount')) {
            $finalPrice = $amount->getValue();
        }
        return parent::_applyOptionsPrice($product, $qty, $finalPrice);
    }

    /**
     * Get product's price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $which
     * @return array
     */
    public function getPrices($product, $which = null)
    {
        return $this->getPricesDependingOnTax($product, $which);
    }

    /**
     * Get price depending on Tax
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $which
     * @param bool $includeTax
     * @return array
     */
    public function getPricesDependingOnTax($product, $which = null, $includeTax = null)
    {
        $giftAmount = $this->getGiftAmount($product);
        //zend_debug::dump($giftAmount);
        if (isset($giftAmount['min_price']) && isset($giftAmount['max_price'])) {
            $minimalPrice = Mage::helper('tax')->getPrice($product, $giftAmount['min_price'], $includeTax);
            $maximalPrice = Mage::helper('tax')->getPrice($product, $giftAmount['max_price'], $includeTax);
        } else {
            $minimalPrice = $maximalPrice = Mage::helper('tax')->getPrice($product, $giftAmount['price'], $includeTax);
        }

        if ($which == 'max') {
            return $maximalPrice;
        } elseif ($which == 'min') {
            return $minimalPrice;
        }
        return array($minimalPrice, $maximalPrice);
    }

    /**
     * Get min price
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getMinimalPrice($product)
    {
        return $this->getPrices($product, 'min');
    }

    /**
     * Get max price
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getMaximalPrice($product)
    {
        return $this->getPrices($product, 'max');
    }

    /**
     * Retrieve product final price
     *
     * @param null $qty
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getFinalPrice($qty = null, $product)
    {
        $finalPrice = $this->getPrice($product);
        $product->setFinalPrice($finalPrice);

        $finalPrice = $product->getData('final_price');
        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
        $finalPrice = max(0, $finalPrice);
        $product->setFinalPrice($finalPrice);

        return $finalPrice;
    }

}
