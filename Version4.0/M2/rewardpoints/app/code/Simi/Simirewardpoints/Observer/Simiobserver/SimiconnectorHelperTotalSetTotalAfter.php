<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Simi\Simirewardpoints\Observer\Simiobserver;

use Magento\Framework\Event\ObserverInterface;

/**
 * Description of SimiconnectorHelperTotalSetTotalAfter
 *
 * @author scott
 */
class SimiconnectorHelperTotalSetTotalAfter implements ObserverInterface{
    
    public $spendHelper;
    public $simiObjectManager;
    public $pointHelper;
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
        $orderTotalHelper = $observer->getObject();
        if (!$this->spendHelper->enableReward()) {
            return;
        }
        $pointSpending = 0;
        $pointDiscount = 0.00;
        $pointEarning = 0;
        $quote = $this->spendHelper->getQuote();
        if ($quote->getSimirewardpointsSpent()) {
            $pointSpending = (int) $quote->getSimirewardpointsSpent();
            $pointDiscount = $quote->getSimirewardpointsDiscount();
        }
        if ($quote->getSimirewardpointsEarn()) {
            $pointEarning = (int) $quote->getSimirewardpointsEarn();
        }

        if ($pointSpending != 0) {
            $orderTotalHelper->addCustomRow(
                __('You will spend'), 5, $pointSpending, $this->pointHelper
                    ->format($pointSpending)
            );
        }
        if ($pointEarning != 0) {
            $orderTotalHelper->addCustomRow(
                __('You will earn'), 6, $pointEarning, $this->pointHelper
                    ->format($pointEarning)
            );
        }
        if ($pointDiscount != 0) {
            $orderTotalHelper->addCustomRow(
                __('Use point'), 7, $pointDiscount
            );
        }
    }

}
