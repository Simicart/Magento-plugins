<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Simi\Simirewardpoints\Observer\Simiobserver;

/**
 * Description of SimiconnectorModelApiOrdersOnepageShowAfter
 *
 * @author scott
 */
class SimiconnectorModelApiOrdersOnepageShowAfter implements \Magento\Framework\Event\ObserverInterface{
    public $spendHelper;
    public $pointHelper;
    public $simiObjectManager;


    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Simi\Simirewardpoints\Helper\Block\Spend $spendHelper,
        \Simi\Simirewardpoints\Helper\Point $pointHelper
    ) {
       $this->simiObjectManager = $objectManager;
       $this->spendHelper = $spendHelper;
       $this->pointHelper = $pointHelper;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer){
        $orderAPIModel = $observer->getObject();
        $detail_onepage = $orderAPIModel->detail_onepage;
        $helper = $this->spendHelper;
        
        if (!$helper->enableReward()) {
            return;
        }    	
        $pointSpending = 0;
    	$pointDiscount = 0.00;
    	$pointEarning  = 0;
        $quote = $helper->getQuote();
        if ($quote->getSimirewardpointsSpent()) {
            $pointSpending = (int) $quote->getSimirewardpointsSpent();
            $pointDiscount = $quote->getSimirewardpointsDiscount();
        }
        if ($quote->getSimirewardpointsEarn()) {
            $pointEarning = (int) $quote->getSimirewardpointsEarn();
        }
       
        $loyalty_array = array();
        $loyalty_array['loyalty_spend'] = $pointSpending;
        $loyalty_array['loyalty_discount'] = $pointDiscount;
        $loyalty_array['loyalty_earn'] = $pointEarning;
        $loyalty_array['loyalty_spending'] = $this->pointHelper->format($pointSpending);
        $loyalty_array['loyalty_earning'] = $this->pointHelper->format($pointEarning);
        $loyalty_array['loyalty_rules'] = $this->getSliderRulesFormatted($helper);
        $detail_onepage['order']['loyalty'] = $loyalty_array;
        $orderAPIModel->detail_onepage = $detail_onepage;
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
                $ruleOptions['needPointLabel'] = $this->pointHelper
                        ->format($ruleOptions['needPoint']);
            } else {
                $quote = $helper->getQuote();

                $ruleOptions['minPoints'] = 0;
                $ruleOptions['pointStep'] = (int) $rule->getPointsSpended();

                $maxPoints = $helper->getCustomerPoint();
                if ($rule->getMaxPointsSpended()
                        && $maxPoints > $rule->getMaxPointsSpended()) {
                    $maxPoints = $rule->getMaxPointsSpended();
                }
                if ($maxPoints > $helper->getCalculation()
                        ->getRuleMaxPointsForQuote($rule, $quote)) {
                    $maxPoints = $helper->getCalculation()
                            ->getRuleMaxPointsForQuote($rule, $quote);
                }
                // Refine max points
                if ($ruleOptions['pointStep']) {
                    $maxPoints = floor($maxPoints / $ruleOptions['pointStep']) * $ruleOptions['pointStep'];
                }
                $ruleOptions['maxPoints'] = max(0, $maxPoints);
                if ($rule->getName()) {
                    $ruleOptions['name'] = $rule->getName();
                }
                $ruleOptions['pointStepLabel'] = $this->pointHelper
                        ->format($ruleOptions['pointStep']);
                $ruleOptions['pointStepDiscount'] = $this->formatDiscount($rule);
                $ruleOptions['optionType'] = 'slider';
            }
            $result[] = $ruleOptions;
        }
        return $result;
    }
    
    public function formatDiscount($rule) {
        $currencyPrice = $this->simiObjectManager
                ->create('\Magento\Framework\Pricing\PriceCurrencyInterface');
        if ($rule->getId() == 'rate') {
            $price = $currencyPrice->convertAndFormat($rule->getBaseRate(),false);
        } else {
            if ($rule->getDiscountStyle() == 'cart_fixed') {
                $price = $currencyPrice->convertAndFormat($rule->getDiscountAmount(),false);
            } else {
                return round($rule->getDiscountAmount(), 2) . '%';
            }
        }
        return $price;
    }
}
