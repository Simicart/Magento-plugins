<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Loyalty
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Loyalty Helper
 * 
 * @category    Magestore
 * @package     Magestore_Loyalty
 * @author      Magestore Developer
 */
class Magestore_Loyalty_Helper_Block_Spend extends Magestore_RewardPoints_Helper_Block_Spend
{
    /**
     * get slider rule formatted
     * 
     * @param array $rules
     * @return string
     */
    public function getSliderRulesFormatted($rules = null)
    {
        if (is_null($rules)) {
            $rules = $this->getSliderRules();
        }
        $result = array();
        foreach ($rules as $rule) {
            $ruleOptions = array('id' => $rule->getId());
            if ($this->getCustomerPoint() < $rule->getPointsSpended()) {
                $ruleOptions['optionType'] = 'needPoint';
                $ruleOptions['needPoint'] = $rule->getPointsSpended() - $this->getCustomerPoint();
                $ruleOptions['needPointLabel'] = Mage::helper('rewardpoints/point')->format($ruleOptions['needPoint']);
            } else {
                $quote = $this->getQuote();
                
                $ruleOptions['minPoints'] = 0;
                $ruleOptions['pointStep'] = (int)$rule->getPointsSpended();
                
                $maxPoints = $this->getCustomerPoint();
                if ($rule->getMaxPointsSpended() && $maxPoints > $rule->getMaxPointsSpended()) {
                    $maxPoints = $rule->getMaxPointsSpended();
                }
                if ($maxPoints > $this->getCalculation()->getRuleMaxPointsForQuote($rule, $quote)) {
                    $maxPoints = $this->getCalculation()->getRuleMaxPointsForQuote($rule, $quote);
                }
                // Refine max points
                if ($ruleOptions['pointStep']) {
                    $maxPoints = floor($maxPoints / $ruleOptions['pointStep']) * $ruleOptions['pointStep'];
                }
                $ruleOptions['maxPoints'] = max(0, $maxPoints);
                if ($rule->getName()) {
                    $ruleOptions['name'] = $rule->getName();
                }
                $ruleOptions['pointStepLabel'] = Mage::helper('rewardpoints/point')->format($ruleOptions['pointStep']);
                $ruleOptions['pointStepDiscount'] = $this->formatDiscount($rule);
                $ruleOptions['optionType'] = 'slider';
            }
            $result[] = $ruleOptions;
        }
        return $result;
    }
    
    /**
     * format discount for a rule
     * 
     * @param Varien_Object $rule
     * @return string
     */
    public function formatDiscount($rule)
    {
        $store = Mage::app()->getStore(Mage::helper('rewardpoints/customer')->getStoreId());
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
}
