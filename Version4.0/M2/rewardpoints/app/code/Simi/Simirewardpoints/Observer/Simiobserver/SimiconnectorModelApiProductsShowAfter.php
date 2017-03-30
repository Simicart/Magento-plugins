<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Simi\Simirewardpoints\Observer\Simiobserver;

/**
 * Description of SimiconnectorModelApiProductsShowAfter
 *
 * @author scott
 */
class SimiconnectorModelApiProductsShowAfter implements \Magento\Framework\Event\ObserverInterface {
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
     * Product Model
     * Simi\Simirewardpoints\Helper\Point
     */

    public $productModel;
    
    /*
     * Core Registry
     * Magento\Framework\Registry
     */
    
    public $coreRegistry;
    
    public $scopeConfig;
    
    const XML_PATH_SHOW_PRODUCT = 'simirewardpoints/display/product';
    
    public function __construct(
        \Simi\Simirewardpoints\Helper\Point $pointHelper,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config
    ) {
        $this->simiObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->pointHelper = $pointHelper;
        $this->productModel = $productModel;
        $this->scopeConfig = $config;
        $this->coreRegistry = $registry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer){
        $productAPIModel = $observer->getObject();
        $detail_info = $productAPIModel->detail_info;
        if (!$this->isShowOnProduct()) {
            return;
        }
        $productId = $detail_info['product']['entity_id'];
        $product = $this->productModel->load($productId);

        $block = $this->simiObjectManager->create('Simi\Simirewardpoints\Block\Product\View\Earning');
        if (!$this->coreRegistry->registry('product')) {
            $this->coreRegistry->register('product', $product);
        }
        
        if ($block->hasEarningRate()) {
            $detail_info['product']['loyalty_image'] = $this->pointHelper->getImage();
            $detail_info['product']['loyalty_label'] = __('You could receive some %1 for purchasing this product', $block->getPluralPointName());
        }
        $productAPIModel->detail_info = $detail_info;
    }
    
    public function isShowOnProduct($store = null) {
        return $this->scopeConfig->getValue(
                    self::XML_PATH_SHOW_PRODUCT,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store
                );
    }
}
