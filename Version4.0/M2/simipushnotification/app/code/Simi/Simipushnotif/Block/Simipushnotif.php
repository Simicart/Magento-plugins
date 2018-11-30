<?php
/**
 * Created by PhpStorm.
 * User: macos
 * Date: 11/15/18
 * Time: 4:50 PM
 */

namespace Simi\Simipushnotif\Block;


class Simipushnotif extends \Magento\Framework\View\Element\Template
{
    public $config;

    /**
     * @param \Simi\Simipushnotif\Block\Context $context
     * @param \Magento\Framework\UrlFactory $urlFactory
     */
    public function __construct(\Simi\Simipushnotif\Block\Context $context)
    {
        $this->config = $context->getConfig();
        parent::__construct($context);
    }

    public function getConfigValue($path)
    {
        return $this->config->getCurrentStoreConfigValue($path);
    }

    public function IsEnableForWebsite(){
        return $this->getConfigValue('simipushnotif/notification/enable');
    }
}