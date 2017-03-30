<?php

/**
 * RewardPoints Update Top Link Block
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Block;

class TopLink extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    public $_objectManager;

    /**
     * @var \Simi\Simirewardpoints\Helper\Config
     */
    protected $helper;

    /**
     * @var
     */
    public $_href;

    /**
     * @var
     */
    protected $_label;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_modelUrl;

    /**
     * @var Name
     */
    protected $_blockName;

    /**
     * TopLink constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Simi\Simirewardpoints\Helper\Config $globalConfig
     * @param \Magento\Customer\Model\Url $url
     * @param Name $blockName
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Framework\App\Http\Context $httpContext,
        \Simi\Simirewardpoints\Helper\Config $globalConfig,
        \Magento\Customer\Model\Url $url,
        \Simi\Simirewardpoints\Block\Name $blockName,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->helper = $globalConfig;
        $this->_modelUrl = $url;
        $this->_blockName = $blockName;
        $this->setTemplate('Simi\Simirewardpoints::simirewardpoints/topLink.phtml');
    }

    public function getHref()
    {
        return $this->_modelUrl->getAccountUrl();
    }

    public function getLabel()
    {
        $nameBlock = $this->_blockName;
        if ($this->helper->getDisplayConfig('toplink')) {
            return __('My Account') . ' (' . $nameBlock->toHtml() . ')';
        } else {
            return __('My Account');
        }
    }

    public function getTitle()
    {
        return __('My Account');
    }

    /**
     * functional block - using to change other block information
     *
     * @return string
     */
    protected function _toHtml()
    {

        return parent::_toHtml();
    }
}
