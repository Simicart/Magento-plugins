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
 * Giftvoucher product helper
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer : Peter
 */

class Simi_Simigiftvoucher_Helper_Giftproduct extends Mage_Core_Helper_Data
{
    protected $_product = null;
    /**
     * Get the price information of Gift Card product 
     *
     * @param Simi_Simigiftvoucher_Model_Product $product
     * @return array
     */
    public function getGiftValue($product)
    {
        //zend_debug::dump($product->debug());die;
        $giftType = $product->getSimigiftType();
        switch ($giftType) {
            case Simi_Simigiftvoucher_Model_Gifttype::GIFT_TYPE_FIX:
                $priceType = $product->getSimigiftPriceType();
                $data =  array(
                    'type_value' => 'fixed',
                    'price' => $this->getGiftPriceByStatic($product),
                    'value' => $product->getSimigiftValue()
                );

                if ($priceType == Simi_Simigiftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_DEFAULT){
                    $data['type_price'] = 'default';
                }
                elseif ($priceType == Simi_Simigiftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_FIX){
                    $data['type_price'] = 'fixed';
                }
                else {
                    $data['type_price'] = 'percent';
                    $data['percent_value'] = $product->getSimigiftPrice();
                }
                return $data;
            case Simi_Simigiftvoucher_Model_Gifttype::GIFT_TYPE_RANGE:
                $data = array('type_value' => 'range', 'from' => $product->getSimigiftFrom(), 'to' => $product->getSimigiftTo());
                $priceType = $product->getSimigiftPriceType();
                if ($priceType == Simi_Simigiftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_DEFAULT) {
                    $data['type_price'] = 'default';
                } else {
                    $data['type_price'] = 'percent';
                    $data['percent_value'] = $product->getSimigiftPrice();
                }
                return $data;

            case Simi_Simigiftvoucher_Model_Gifttype::GIFT_TYPE_DROPDOWN:
                $options = explode(',', $product->getSimigiftDropdown());
                $giftPrices = explode(',', $product->getSimigiftPrice());

                foreach ($options as $key => $option) {
                    if (!is_numeric($option) || $option <= 0) {
                        unset($options[$key]);
                    }
                }

                $data = array('type_value' => 'dropdown', 'options_value' => $options);
                $priceType = $product->getSimigiftPriceType();
                if ($priceType == Simi_Simigiftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_DEFAULT) {
                    $data['type_price'] = 'default';
                    $data['prices_dropdown'] = $options;
                } else if ($priceType == Simi_Simigiftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_FIX) {
                    $data['type_price'] = 'fixed';
                    $optionsPrice = explode(',', $product->getSimigiftPrice());
                    $data['prices_dropdown'] = $optionsPrice;
                } else {
                    $data['type_price'] = 'percent';
                    if (count($giftPrices) == count($options)) {
                        for ($i = 0; $i < count($giftPrices); $i++) {
                            $data['prices_dropdown'][] = $giftPrices[$i] * $options[$i] / 100;
                        }
                    } else {
                        foreach ($options as $value) {
                            $data['prices_dropdown'][] = $value * $product->getSimigiftPrice() / 100;
                        }
                    }
                }

                return $data;
            default:
                $giftValue = Mage::helper('simigiftvoucher')->getInterfaceConfig('amount');
                $options = explode(',', $giftValue);
                return array('type' => 'dropdown', 'options' => $options, 'prices' => $options);
        }
    }

    /**
     * Get the static price of Gift Card product 
     *
     * @param Simi_Simigiftvoucher_Model_Product $product
     * @return float
     */
    public function getGiftPriceByStatic($product)
    {
        $giftValue = $product->getSimigiftValue();
        $giftPrice = $product->getSimigiftPrice();
        if ($product->getSimigiftPriceType() == Simi_Simigiftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_DEFAULT) {
            return $giftValue;
        } else if ($product->getSimigiftPriceType() == Simi_Simigiftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_FIX) {
            return $giftPrice;
        } else {
            return $giftValue * $giftPrice / 100;
        }
    }

    public function helper($helper)
    {
        return Mage::helper($helper);
    }

    public function formatPriceFromProduct($_product,$is_detail=false){
        $this->_product = $_product;
        $priceV2 = array();

        $giftAmount = $_product->getPriceModel()->getGiftAmount($_product);
        list($_minimalPriceTax, $_maximalPriceTax) = $_product->getPriceModel()->getPrices($_product);
        list($_minimalPriceInclTax, $_maximalPriceInclTax) = $_product->getPriceModel()->getPricesDependingOnTax($_product, null, true);
        $_id = $_product->getId();
        //zend_debug::dump($giftAmount);die;
        $_weeeTaxAmount = 0;
        if ($giftAmount['price_type'] == 1) {
            $_productFinalprice = $_product->getPriceModel()->getFinalPrice(null, $_product);
            $_finalPrice = Mage::helper('tax')->getPrice($_product, $_productFinalprice);
            $_finalPriceInclTax = Mage::helper('tax')->getPrice($_product, $_productFinalprice, true);
            $_weeeTaxAmount = Mage::helper('weee')->getAmount($_product);
            if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($_product, array(0, 1, 4))) {
                $_minimalPriceTax += $_weeeTaxAmount;
                $_minimalPriceInclTax += $_weeeTaxAmount;
                $_finalPrice += $_weeeTaxAmount;
                $_finalPriceInclTax += $_weeeTaxAmount;
            }
            if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($_product, 2)) {
                $_minimalPriceInclTax += $_weeeTaxAmount;
                $_finalPriceInclTax += $_weeeTaxAmount;
            }
            if (Mage::helper('weee')->typeOfDisplay($_product, array(1, 2, 4)))
                $_weeeTaxAttributes = Mage::helper('weee')->getProductWeeeAttributesForDisplay($_product);
        }

        // type price = fixed
        if ($giftAmount['price_type'] == 1){
            if ($_minimalPriceTax == $_finalPrice){
                $priceV2['minimal_price'] = 1;
                if ($this->displayBothPrices()){
                    $priceV2['show_ex_in_price'] = 1;
                    $priceV2['price_excluding_tax']['price_label'] = Mage::helper('tax')->__('Excl. Tax');
                    $priceV2['price_excluding_tax']['price'] = Mage::helper('core')->currency($_minimalPriceTax,false);
                    if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($_product, array(2, 1, 4))){
                        $_weeeSeparator = '';

                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute){
                            if (Mage::helper('weee')->typeOfDisplay($_product, array(2, 4))){
                                $amount = $_weeeTaxAttribute->getAmount() + $_weeeTaxAttribute->getTaxAmount();
                            }
                            else {
                                $amount = $_weeeTaxAttribute->getAmount();
                            }
                            $_weeeSeparator .= $_weeeTaxAttribute->getName();
                            $_weeeSeparator .= ": ";
                            $_weeeSeparator .= Mage::helper('core')->currency($amount, true, true);
                            $_weeeSeparator .= ' + ';
                            $priceV2['weee'] = $_weeeSeparator;
                        }
                        $this->setWeePrice($priceV2, $_weeeSeparator);
                        $priceV2['show_weee_price'] = 1;
                    }
                    $priceV2['price_including_tax']['price_label'] = Mage::helper('tax')->__('Incl. Tax');
                    $priceV2['price_including_tax']['price'] = Mage::helper('core')->currency($_minimalPriceInclTax,false);
                }
                else {
                    $priceV2['show_ex_in_price'] = 0;
                    $this->setTaxPrice($priceV2, $_minimalPriceTax);
                    if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($_product, array(2, 1, 4))){
                        $_weeeSeparator = '';
                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute){
                            if (Mage::helper('weee')->typeOfDisplay($_product, array(2, 4))){
                                $amount = $_weeeTaxAttribute->getAmount() + $_weeeTaxAttribute->getTaxAmount();
                            }
                            else {
                                $amount = $_weeeTaxAttribute->getAmount();
                            }
                            $_weeeSeparator .= $_weeeTaxAttribute->getName();
                            $_weeeSeparator .= ": ";
                            $_weeeSeparator .= Mage::helper('core')->currency($amount, true, true);
                            $_weeeSeparator .= ' + ';
                            $priceV2['weee'] = $_weeeSeparator;
                        }
                        $this->setWeePrice($priceV2, $_weeeSeparator);
                        $priceV2['show_weee_price'] = 1;
                    }
                    if (Mage::helper('weee')->typeOfDisplay($_product, 2) && $_weeeTaxAmount){
                        $this->setTaxPriceIn($priceV2, $_minimalPriceInclTax);
                    }
                }
            }
            // $_minimalPriceTax != $_finalPrice
            else {
                $priceV2['minimal_price'] = 0;
                $priceV2['has_special_price'] = 1;
                $priceV2['old_price']['price_label'] = Mage::helper('catalog')->__('Regular Price');
                $priceV2['old_price']['price'] = Mage::helper('core')->currency($_minimalPriceTax);
                $priceV2['special_price']['price_label'] = Mage::helper('catalog')->__('Special Price');
                if ($this->displayBothPrices()){
                    $priceV2['show_ex_in_price'] = 1;
                    $priceV2['special_price']['price_excluding_tax']['label'] = Mage::helper('tax')->__('Excl. Tax');
                    $priceV2['special_price']['price_excluding_tax']['price'] = Mage::helper('core')->currency($_finalPrice);

                    if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($_product, array(2, 1, 4))){
                        $_weeeSeparator = '';
                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute){
                            if (Mage::helper('weee')->typeOfDisplay($_product, array(2, 4))){
                                $amount = $_weeeTaxAttribute->getAmount() + $_weeeTaxAttribute->getTaxAmount();
                            }
                            else {
                                $amount = $_weeeTaxAttribute->getAmount();
                            }
                            $_weeeSeparator .= $_weeeTaxAttribute->getName();
                            $_weeeSeparator .= ": ";
                            $_weeeSeparator .= Mage::helper('core')->currency($amount, true, true);
                            $_weeeSeparator .= ' + ';
                            $priceV2['weee'] = $_weeeSeparator;
                        }
                        $this->setWeePrice($priceV2, $_weeeSeparator);
                        $priceV2['show_weee_price'] = 1;
                    }

                    $priceV2['special_price']['price_including_tax']['label'] = Mage::helper('tax')->__('Incl. Tax');
                    $priceV2['special_price']['price_including_tax']['price'] = Mage::helper('core')->currency($_finalPriceInclTax,false);
                }
                // !$this->displayBothPrices()
                else {
                    $priceV2['show_ex_in_price'] = 0;
                    $this->setTaxPrice($priceV2, $_finalPrice);

                    if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($_product, array(2, 1, 4))){
                        $_weeeSeparator = '';
                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute){
                            if (Mage::helper('weee')->typeOfDisplay($_product, array(2, 4))){
                                $amount = $_weeeTaxAttribute->getAmount() + $_weeeTaxAttribute->getTaxAmount();
                            }
                            else {
                                $amount = $_weeeTaxAttribute->getAmount();
                            }
                            $_weeeSeparator .= $_weeeTaxAttribute->getName();
                            $_weeeSeparator .= ": ";
                            $_weeeSeparator .= Mage::helper('core')->currency($amount, true, true);
                            $_weeeSeparator .= ' + ';
                            $priceV2['weee'] = $_weeeSeparator;
                        }
                        $this->setWeePrice($priceV2, $_weeeSeparator);
                        $priceV2['show_weee_price'] = 1;
                    }
                    if (Mage::helper('weee')->typeOfDisplay($_product, 2) && $_weeeTaxAmount){
                        $this->setTaxPriceIn($priceV2, $_finalPriceInclTax);
                    }
                }
            }
        }
        // if price type != 1
        else {
            $priceV2['minimal_price'] = 0;
            $priceV2['product_from_label'] = $this->helper('catalog')->__('From') ;
            $priceV2['product_to_label'] = $this->helper('catalog')->__('To') ;
            $priceV2['show_from_to_tax_price'] = 1;

            if ($this->displayBothPrices()){
                $priceV2['show_ex_in_price'] = 1;
                $this->setBothTaxFromPrice($priceV2, $_minimalPriceTax, $_minimalPriceInclTax);
                $this->setBothTaxToPrice($priceV2, $_maximalPriceTax, $_maximalPriceInclTax);

                if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($_product, array(2, 1, 4))){
                    $_weeeSeparator = '';
                    foreach ($_weeeTaxAttributes as $_weeeTaxAttribute){
                        if (Mage::helper('weee')->typeOfDisplay($_product, array(2, 4))){
                            $amount = $_weeeTaxAttribute->getAmount() + $_weeeTaxAttribute->getTaxAmount();
                        }
                        else {
                            $amount = $_weeeTaxAttribute->getAmount();
                        }
                        $_weeeSeparator .= $_weeeTaxAttribute->getName();
                        $_weeeSeparator .= ": ";
                        $_weeeSeparator .= Mage::helper('core')->currency($amount, true, false);
                        $_weeeSeparator .= ' + ';
                        $priceV2["weee_from"] = $_weeeSeparator;
                        $priceV2["weee_to"] = $_weeeSeparator;
                    }
                    $this->setWeePrice($priceV2, $_weeeSeparator);
                    $priceV2['show_weee_price'] = 1;
                }
            }
            // !$this->displayBothPrices()
            else {
                $this->setTaxFromPrice($priceV2, $_minimalPriceTax);
                $this->setTaxToPrice($priceV2, $_maximalPriceTax);
                $priceV2['show_ex_in_price'] = 0;
                if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($_product, array(2, 1, 4))){
                    $wee = '';
                    foreach ($_weeeTaxAttributes as $_weeeTaxAttribute){
                        if (Mage::helper('weee')->typeOfDisplay($_product, array(2, 4))){
                            $amount = $_weeeTaxAttribute->getAmount()+$_weeeTaxAttribute->getTaxAmount();
                        }else{
                            $amount = $_weeeTaxAttribute->getAmount();
                        }
                        $wee .= $_weeeTaxAttribute->getName();;
                        $wee .= ": ";
                        $wee .= Mage::helper('core')->currency($amount, true, false);
                        $wee .= " + ";
                        $priceV2["weee"] = $wee;
                    }
                    $this->setWeePrice($priceV2, $wee);
                    $priceV2['show_weee_price'] = 1;
                }
                if (Mage::helper('weee')->typeOfDisplay($_product, 2) && $_weeeTaxAmount){
                    $this->setTaxFromPrice($priceV2, $_minimalPriceInclTax);
                    $this->setTaxToPrice($priceV2, $_maximalPriceInclTax);
                }
            }

            if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($_product, array(0, 1, 4))) {
                $_maximalPriceTax += $_weeeTaxAmount;
                $_maximalPriceInclTax += $_weeeTaxAmount;
            }
            if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($_product, 2)){
                $_maximalPriceInclTax += $_weeeTaxAmount;
            }

            if ($_maximalPriceTax > $_minimalPriceTax){
                $priceV2['show_from_to_tax_price'] = 1;
                if ($this->displayBothPrices()){
                    $priceV2['show_ex_in_price'] = 1;
                    $priceV2['product_from_label'] = $this->helper('catalog')->__('From') ;
                    $priceV2['product_to_label'] = $this->helper('catalog')->__('To') ;

                    $this->setTaxFromPrice($priceV2, $_minimalPriceInclTax);
                    $this->setTaxToPrice($priceV2, $_maximalPriceInclTax);

                    if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($_product, array(2, 1, 4))){
                        $wee = '';
                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute){
                            if (Mage::helper('weee')->typeOfDisplay($_product, array(2, 4))){
                                $amount = $_weeeTaxAttribute->getAmount()+$_weeeTaxAttribute->getTaxAmount();
                            }else{
                                $amount = $_weeeTaxAttribute->getAmount();
                            }
                            $wee .= $_weeeTaxAttribute->getName();;
                            $wee .= ": ";
                            $wee .= Mage::helper('core')->currency($amount, true, false);
                            $wee .= " + ";
                            $priceV2["weee"] = $wee;
                        }
                        $this->setWeePrice($priceV2, $wee);
                        $priceV2['show_weee_price'] = 1;
                    }
                }
                // !$this->displayBothPrices()
                else {
                    $priceV2['show_ex_in_price'] = 0;
                    $this->setTaxPrice($priceV2,$_maximalPriceTax);
                    if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($_product, array(2, 1, 4))){
                        $wee = '';
                        foreach ($_weeeTaxAttributes as $_weeeTaxAttribute){
                            if (Mage::helper('weee')->typeOfDisplay($_product, array(2, 4))){
                                $amount = $_weeeTaxAttribute->getAmount()+$_weeeTaxAttribute->getTaxAmount();
                            }else{
                                $amount = $_weeeTaxAttribute->getAmount();
                            }
                            $wee .= $_weeeTaxAttribute->getName();;
                            $wee .= ": ";
                            $wee .= Mage::helper('core')->currency($amount, true, false);
                            $wee .= " + ";
                            $priceV2["weee"] = $wee;
                        }
                        $this->setWeePrice($priceV2, $wee);
                        $priceV2['show_weee_price'] = 1;
                    }
                    if (Mage::helper('weee')->typeOfDisplay($_product, 2) && $_weeeTaxAmount){
                        $this->setTaxPrice($priceV2, $_maximalPriceInclTax);
                    }
                }
            }

        }

        return $priceV2;
    }

    public function displayBothPrices()
    {
        return Mage::helper('tax')->displayBothPrices();
    }

    public function setWeePrice(&$price, $wee)
    {
        $price['wee'] = $wee;
    }

    /**
     * @param $price
     * @param $_price
     * show type
     * 3 show price only.
     * 4 show price - wee.
     * 5 show wee - price.
     */
    public function setTaxPrice(&$price, $_price)
    {
        $_coreHelper = Mage::helper('core');
        // $price['show_type'] = 3;
        $price['price'] = $_coreHelper->currency($_price, false, false);
    }

    public function setTaxPriceIn(&$price, $_price)
    {
        $_coreHelper = Mage::helper('core');
        // $price['show_type'] = 3;
        $price['price_in'] = $_coreHelper->currency($_price, false, false);
    }

    public function setTaxFromPrice(&$price, $_price)
    {
        $_coreHelper = Mage::helper('core');
        // $price['show_type'] = 3;
        $price['from_price'] = $_coreHelper->currency($_price, false, false);
    }

    public function setTaxToPrice(&$price, $_price)
    {
        $_coreHelper = Mage::helper('core');
        // $price['show_type'] = 3;
        $price['to_price'] = $_coreHelper->currency($_price, false, false);
    }

    /**
     * @param $price
     * @param $_exclTax
     * @param $_inclTax
     * type
     * 0 show price only
     * 1 show ex + wee + in
     * 2 show  ex + in + wee
     */
    public function setBothTaxPrice(&$price, $_exclTax, $_inclTax)
    {
        $_coreHelper = $this->helper('core');
        $price['price_excluding_tax'] = array(
            'label' => $this->helper('tax')->__('Excl. Tax'),
            'price' => $_coreHelper->currency($_exclTax, false, false),
        );
        $price['price_including_tax'] = array(
            'label' => $this->helper('tax')->__('Incl. Tax'),
            'price' => $_coreHelper->currency($_inclTax, false, false),
        );
    }

    public function setBothTaxFromPrice(&$price, $_exclTax, $_inclTax)
    {
        $_coreHelper = $this->helper('core');
        $price['from_price_excluding_tax'] = array(
            'label' => $this->helper('tax')->__('Excl. Tax'),
            'price' => $_coreHelper->currency($_exclTax, false, false),
        );
        $price['from_price_including_tax'] = array(
            'label' => $this->helper('tax')->__('Incl. Tax'),
            'price' => $_coreHelper->currency($_inclTax, false, false),
        );
    }

    public function setBothTaxToPrice(&$price, $_exclTax, $_inclTax)
    {
        $_coreHelper = $this->helper('core');
        $price['to_price_excluding_tax'] = array(
            'label' => $this->helper('tax')->__('Excl. Tax'),
            'price' => $_coreHelper->currency($_exclTax, false, false),
        );
        $price['to_price_including_tax'] = array(
            'label' => $this->helper('tax')->__('Incl. Tax'),
            'price' => $_coreHelper->currency($_inclTax, false, false),
        );
    }
}
