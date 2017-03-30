<?php

/**
 * Promoteapp Content helper
 */

namespace Simi\Simipromoteapp\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public $simiObjectManager;
    public $storeManager;
    public $transportBuilder;
    
    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    public $formFactory;
    public $dateFactory;
    public $resource;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Magento\Framework\Data\Form\Element\DateFactory $dateFactory,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->simiObjectManager    = $simiObjectManager;
        $this->scopeConfig = $this->simiObjectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
        $this->storeManager  = $this->simiObjectManager->create('\Magento\Store\Model\StoreManagerInterface');
        $this->transportBuilder = $transportBuilder;
        $this->formFactory = $formFactory;
        $this->dateFactory = $dateFactory;
        $this->resource = $resource;
        parent::__construct($context);
    }
    
    public function getImageLink($media_path)
    {
        return $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . 'simi/simipromoteapp/promotepage/' . $media_path;
    }
    
    public function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }
}
