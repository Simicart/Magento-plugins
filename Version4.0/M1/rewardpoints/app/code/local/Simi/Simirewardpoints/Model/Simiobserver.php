<?php

class Simi_Simirewardpoints_Model_Simiobserver {

    const XML_PATH_SHOW_PRODUCT = 'simirewardpoints/loyalty/product';
    const XML_PATH_SHOW_CART = 'simirewardpoints/loyalty/cart';

    public function simiSimiconnectorModelServerInitialize($observer) {
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();
        if ($observerObjectData['resource'] == 'simirewardpoints') {
            $observerObjectData['module'] = 'simirewardpoints';
        } else if ($observerObjectData['resource'] == 'simirewardpointstransactions') {
            $observerObjectData['module'] = 'simirewardpoints';
        }
        $observerObject->setData($observerObjectData);
    }

    public function simiSimiconnectorModelApiProductsShowAfter($observer) {
        $productAPIModel = $observer->getObject();
        $detail_info = $productAPIModel->detail_info;
        if (!$this->isShowOnProduct()) {
            return;
        }
        $productId = $detail_info['product']['entity_id'];
        $product = Mage::getModel('catalog/product')->load($productId);

        $block = Mage::getBlockSingleton('simirewardpoints/product_view_earning');
        if (!Mage::registry('product')) {
            Mage::register('product', $product);
        }
        if ($block->hasEarningRate()) {
            $detail_info['product']['loyalty_image'] = Mage::helper('simirewardpoints/point')->getImage();
            $detail_info['product']['loyalty_label'] = $block->__('You could receive some %s for purchasing this product', $block->getPluralPointName());
        }
        $productAPIModel->detail_info = $detail_info;
    }

    public function simiSimiconnectorModelApiQuoteitemsIndexAfter($observer) {
        $quoteItemAPIModel = $observer->getObject();
        $detail_list = $quoteItemAPIModel->detail_list;
        if (!$this->isShowOnCart()) {
            return;
        }
        $earningPoints = Mage::helper('simirewardpoints/calculation_earning')->getTotalPointsEarning();
        if ($earningPoints) {
            $label = Mage::helper('simirewardpoints')->__('Checkout now and earn %s in rewards', Mage::helper('simirewardpoints/point')->format($earningPoints)
            );
            $detail_list['loyalty']['loyalty_image'] = Mage::helper('simirewardpoints/point')->getImage();
            $detail_list['loyalty']['loyalty_label'] = $label;
        }
        $quoteItemAPIModel->detail_list = $detail_list;
    }

    public function simiSimiconnectorModelApiOrdersOnepageShowAfter($observer) {
        $orderAPIModel = $observer->getObject();
        $detail_onepage = $orderAPIModel->detail_onepage;
        $helper = Mage::helper('simirewardpoints/block_spend');
        if (!$helper->enableReward()) {
            return;
        }    	
        $pointSpending = 0;
    	$pointDiscount = 0.00;
    	$pointEarning  = 0;
        foreach ($helper->getQuote()->getAllAddresses() as $address) {
            if ($address->getSimirewardpointsSpent()) {
                $pointSpending = (int) $address->getSimirewardpointsSpent();
                $pointDiscount = $address->getSimirewardpointsDiscount();
            }
            if ($address->getSimirewardpointsEarn()) {
                $pointEarning = (int) $address->getSimirewardpointsEarn();
            }
        }
        $loyalty_array = array();
        $loyalty_array['loyalty_spend'] = $pointSpending;
        $loyalty_array['loyalty_discount'] = $pointDiscount;
        $loyalty_array['loyalty_earn'] = $pointEarning;
        $loyalty_array['loyalty_spending'] = Mage::helper('simirewardpoints/point')->format($pointSpending);
        $loyalty_array['loyalty_earning'] = Mage::helper('simirewardpoints/point')->format($pointEarning);
        $loyalty_array['loyalty_rules'] = $this->getSliderRulesFormatted($helper, null);
        $detail_onepage['order']['loyalty'] = $loyalty_array;
        $orderAPIModel->detail_onepage = $detail_onepage;
    }


    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }

    public function simiSimiconnectorHelperTotalSetTotalAfter($observer) {
        $orderTotalHelper = $observer->getObject();

        $helper = Mage::helper('simirewardpoints/block_spend');
        if (!$helper->enableReward()) {
            return;
        }
        $pointSpending = 0;
        $pointDiscount = 0.00;
        $pointEarning = 0;

        $pointSpending = Mage::helper('simirewardpoints/calculation_spending')->getTotalPointSpent();
        if (Mage::helper('simirewardpoints/calculation_spending')->getTotalPointSpent() &&
            !Mage::getStoreConfigFlag('simirewardpoints/earning/earn_when_spend',Mage::app()->getStore()->getId()))
            $pointEarning = 0;
        else
            $pointEarning = Mage::helper('simirewardpoints/calculation_earning')->getTotalPointsEarning();
        $pointDiscount = $this->_getQuote()->getSimirewardpointsDiscount();

        if ($pointSpending != 0)
            $orderTotalHelper->addCustomRow(Mage::helper('simirewardpoints')->__('You will spend'), 5, $pointSpending, Mage::helper('simirewardpoints/point')->format($pointSpending));
        if ($pointEarning != 0)
            $orderTotalHelper->addCustomRow(Mage::helper('simirewardpoints')->__('You will earn'), 6, $pointEarning, Mage::helper('simirewardpoints/point')->format($pointEarning));
        if ($pointDiscount != 0)
            $orderTotalHelper->addCustomRow(Mage::helper('simirewardpoints')->__('Use point'), 7, $pointDiscount);
    }

    public function getSliderRulesFormatted($helper, $rules = null) {
        if (is_null($rules)) {
            $rules = $helper->getSliderRules();
        }
        $result = array();
        foreach ($rules as $rule) {
            $ruleOptions = array('id' => $rule->getId());
            if ($helper->getCustomerPoint() < $rule->getPointsSpended()) {
                $ruleOptions['optionType'] = 'needPoint';
                $ruleOptions['needPoint'] = $rule->getPointsSpended() - $helper->getCustomerPoint();
                $ruleOptions['needPointLabel'] = Mage::helper('simirewardpoints/point')->format($ruleOptions['needPoint']);
            } else {
                $quote = $helper->getQuote();

                $ruleOptions['minPoints'] = 0;
                $ruleOptions['pointStep'] = (int) $rule->getPointsSpended();

                $maxPoints = $helper->getCustomerPoint();
                if ($rule->getMaxPointsSpended() && $maxPoints > $rule->getMaxPointsSpended()) {
                    $maxPoints = $rule->getMaxPointsSpended();
                }
                if ($maxPoints > $helper->getCalculation()->getRuleMaxPointsForQuote($rule, $quote)) {
                    $maxPoints = $helper->getCalculation()->getRuleMaxPointsForQuote($rule, $quote);
                }
                // Refine max points
                if ($ruleOptions['pointStep']) {
                    $maxPoints = floor($maxPoints / $ruleOptions['pointStep']) * $ruleOptions['pointStep'];
                }
                $ruleOptions['maxPoints'] = max(0, $maxPoints);
                if ($rule->getName()) {
                    $ruleOptions['name'] = $rule->getName();
                }
                $ruleOptions['pointStepLabel'] = Mage::helper('simirewardpoints/point')->format($ruleOptions['pointStep']);
                $ruleOptions['pointStepDiscount'] = $this->formatDiscount($rule);
                $ruleOptions['optionType'] = 'slider';
            }
            $result[] = $ruleOptions;
        }
        return $result;
    }

    public function formatDiscount($rule) {
        $store = Mage::app()->getStore(Mage::helper('simirewardpoints/customer')->getStoreId());
        if ($rule->getId() == 'rate') {
            $price = $store->convertPrice($rule->getBaseRate(), true, false);
        } else {
            if ($rule->getDiscountStyle() == 'cart_fixed') {
                $price = $store->convertPrice($rule->getDiscountAmount(), true, false);
            } else {
                return round($rule->getDiscountAmount(), 2) . '%';
            }
        }
        return $price;
    }

    public function isShowOnProduct($store = null) {
        return Mage::getStoreConfigFlag(self::XML_PATH_SHOW_PRODUCT, $store);
    }

    public function isShowOnCart($store = null) {
        return Mage::getStoreConfigFlag(self::XML_PATH_SHOW_CART, $store);
    }

}
