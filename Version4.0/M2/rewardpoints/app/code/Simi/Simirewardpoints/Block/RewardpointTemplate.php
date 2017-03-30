<?php

/**
 * Simirewardpoints Core Block Template Block
 * You should write block extended from this block when you write plugin
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Block;

class RewardpointTemplate extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    public $_objectManager;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var
     */
    public $scopeConfig;

    /**
     * RewardpointTemplate constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data
    ) {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_moduleManager = $this->_objectManager->create('Magento\Framework\Module\Manager');
        $this->scopeConfig = $this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        parent::__construct($context, $data);
    }

    public function getStoreConfig($path)
    {
        return $this->_scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * check reward points system is enabled or not
     *
     * @return boolean
     */
    public function isEnable()
    {
        return $this->_objectManager->create('Simi\Simirewardpoints\Helper\Data')->isEnable();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->isEnable()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * @param $plugin
     * @return bool
     */
    public function isPluginEnable($plugin)
    {
        if ($this->_moduleManager->isEnabled($plugin)) {
            return true;
        }
        return false;
    }

    public function createUrl($url, $params = [])
    {
        return $this->_urlBuilder->getDirectUrl($url, $params);
    }
}
