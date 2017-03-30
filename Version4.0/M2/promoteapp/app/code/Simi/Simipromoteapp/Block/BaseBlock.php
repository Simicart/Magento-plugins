<?php

/**
 * Copyright © 2017 Simi . All rights reserved.
 */

namespace Simi\Simipromoteapp\Block;

use Magento\Framework\UrlFactory;

class BaseBlock extends \Magento\Framework\View\Element\Template
{

    public $simiObjectManager;
    public $scopeConfig;

    /**
     * @var \Simi\Simipromoteapp\Helper\Data
     */
    protected $_devToolHelper;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_urlApp;

    /**
     * @var \Simi\Simipromoteapp\Model\Config
     */
    protected $_config;

    /**
     * @param \Simi\Simipromoteapp\Block\Context $context
     * @param \Magento\Framework\UrlFactory $urlFactory
     */
    public function __construct(
        \Simi\Simipromoteapp\Block\Context $context
    ) {
        $this->simiObjectManager  = $context->getObjectManager();
        $this->_devToolHelper = $context->getSimipromoteappHelper();
        $this->_config        = $context->getConfig();
        $this->scopeConfig = $this->simiObjectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
        $this->_urlApp        = $context->getUrlFactory()->create();
        parent::__construct($context);
    }

    /**
     * Function for getting event details
     * @return array
     */
    public function getEventDetails()
    {
        return $this->_devToolHelper->getEventDetails();
    }

    /**
     * Function for getting current url
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlApp->getCurrentUrl();
    }

    /**
     * Function for getting controller url for given router path
     * @param string $routePath
     * @return string
     */
    public function getControllerUrl($routePath)
    {

        return $this->_urlApp->getUrl($routePath);
    }

    /**
     * Function for getting current url
     * @param string $path
     * @return string
     */
    public function getConfigValue($path)
    {
        return $this->_config->getCurrentStoreConfigValue($path);
    }

    /**
     * Function canShowSimipromoteapp
     * @return bool
     */
    public function canShowSimipromoteapp()
    {
        $isEnabled = $this->getConfigValue('simipromoteapp/module/is_enabled');
        if ($isEnabled) {
            $allowedIps = $this->getConfigValue('simipromoteapp/module/allowed_ip');
            if (is_null($allowedIps)) {
                return true;
            } else {
                $remoteIp = $_SERVER['REMOTE_ADDR'];
                if (strpos($allowedIps, $remoteIp) !== false) {
                    return true;
                }
            }
        }
        return false;
    }
    
    public function getConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }
    
    public function getCurrentStoreConfigValue($path)
    {
        return $this->scopeConfig->getValue('simipromoteapp/'.$path);
    }
}
