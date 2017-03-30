<?php
/**
 * SimirewardPoints Policy Helper
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */
namespace Simi\Simirewardpoints\Helper;

use Magento\Framework\App\Helper\Context;

class Policy extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_SHOW_POLICY  = 'simirewardpoints/general/show_policy_menu';
    const XML_PATH_POLICY_PAGE  = 'simirewardpoints/general/policy_page';
    const XML_PATH_SHOW_WELCOME  = 'simirewardpoints/general/show_welcome_page';
    protected $helper;

    public function __construct(
        Context $context,
        \Simi\Simirewardpoints\Helper\Config $globalConfig
    ) {
    
        parent::__construct($context);
        $this->helper = $globalConfig;
    }

    /**
     * get Policy URL, return the url to view Policy
     *
     * @return string
     */
    public function getPolicyUrl()
    {
        if (!$this->helper->getConfig(self::XML_PATH_SHOW_POLICY)) {
            return $this->_urlBuilder->getDirectUrl('simirewardpoints/index/index/');
        }
        return $this->_urlBuilder->getDirectUrl('simirewardpoints/policy/');
    }

    public function getWelcomeUrl()
    {
        if (!$this->helper->getConfig(self::XML_PATH_SHOW_WELCOME)) {
            return $this->_urlBuilder->getDirectUrl('simirewardpoints/index/index/');
        }
        return $this->_urlBuilder->getUrl(null, ['_direct' => $this->helper->getConfig('simirewardpoints/general/welcome_page')]);
    }
    
    /**
     * Check policy menu configuration
     *
     * @param mixed $store
     * @return boolean
     */
    public function showPolicyMenu($store = null)
    {
        return $this->helper->getConfig(self::XML_PATH_SHOW_POLICY, $store);
    }
}
