<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Simi\Simirewardpoints\Observer\Simiobserver;

/**
 * Description of SimiconnectorModelApiQuoteitemsIndexAfter
 *
 * @author scott
 */
class SimiconnectorModelApiQuoteitemsIndexAfter implements \Magento\Framework\Event\ObserverInterface{
    /*
     * Object Manager
     * \Magento\Framework\ObjectManagerInterface
     */

    public $simiObjectManager;
    
    /*
     * Point Helper
     * Simi\Simirewardpoints\Helper\Point
     */

    public $pointHelper;
    
    /*
     * Point Helper
     * Simi\Simirewardpoints\Helper\Calculation\Earning
     */

    public $earningHelper;
    
    
    /*
     * Core Registry
     * Magento\Framework\Registry
     */
    
    public $scopeConfig;
    
    const XML_PATH_SHOW_CART = 'simirewardpoints/display/minicart';
    
    public function __construct(
        \Simi\Simirewardpoints\Helper\Point $pointHelper,
        \Magento\Catalog\Model\Product $productModel,
        \Simi\Simirewardpoints\Helper\Calculation\Earning $earningHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $config
    ) {
        $this->simiObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->pointHelper = $pointHelper;
        $this->earningHelper = $earningHelper;
        $this->productModel = $productModel;
        $this->scopeConfig = $config;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer){
        $quoteItemAPIModel = $observer->getObject();
        $detail_list = $quoteItemAPIModel->detail_list;
        if (!$this->isShowOnCart()) {
            return;
        }
        $earningPoints = $this->earningHelper->getTotalPointsEarning();
        if ($earningPoints) {
            $label =__('Checkout now and earn %1 in rewards',
                    $this->pointHelper->format($earningPoints)
            );
            $detail_list['loyalty']['loyalty_image'] = $this->pointHelper->getImage();
            $detail_list['loyalty']['loyalty_label'] = $label;
        }
        $quoteItemAPIModel->detail_list = $detail_list;
    }

    public function isShowOnCart($store = null) {
        return $this->scopeConfig->getValue(
                    self::XML_PATH_SHOW_CART,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store
                );
    }
}
