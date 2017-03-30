<?php

/**
 * RewardPoints Calculation Helper Abstract
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Helper\Calculation;

use Magento\Framework\App\Helper\Context;

class AbstractCalculation extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    protected $_objectManager;

    /**
     * AbstractCalculation constructor.
     * @param Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    /**
     * Cache helper data to Memory
     *
     * @var array
     */
    protected $_cacheRule = [];

    /**
     * check cache is existed or not
     *
     * @param string $cacheKey
     * @return boolean
     */
    public function hasCache($cacheKey)
    {
        if (array_key_exists($cacheKey, $this->_cacheRule)) {
            return true;
        }
        return false;
    }

    /**
     * save value to cache
     *
     * @param string $cacheKey
     * @param mixed $value
     * @return \Simi\Simirewardpoints\Helper\Calculation\Abstract
     */
    public function saveCache($cacheKey, $value = null)
    {
        $this->_cacheRule[$cacheKey] = $value;
        return $this;
    }

    /**
     * get cache value by cache key
     *
     * @param  $cacheKey
     * @return mixed
     */
    public function getCache($cacheKey)
    {
        if (array_key_exists($cacheKey, $this->_cacheRule)) {
            return $this->_cacheRule[$cacheKey];
        }
        return null;
    }

    /**
     * get customer group id, depend on current checkout session (admin, frontend)
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        if (!$this->hasCache('abstract_customer_group_id')) {
            $app_state = $this->_objectManager->get('Magento\Framework\App\State');
            $area_code = $app_state->getAreaCode();
            if ($area_code == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
                $customer = $this->_objectManager->get('Magento\Backend\Model\Session\Quote')->getQuote()->getCustomer();
                $this->saveCache('abstract_customer_group_id', $customer->getGroupId());
            } else {
                $this->saveCache('abstract_customer_group_id', $this->_customerSession->getCustomerGroupId());
            }
        }
        return $this->getCache('abstract_customer_group_id');
    }

    /**
     * get Website ID, depend on current checkout session (admin, frontend)
     *
     * @return int
     */
    public function getWebsiteId()
    {
        if (!$this->hasCache('abstract_website_id')) {
            if ($this->_storeManager->getStore()->getCode() == \Magento\Store\Model\Store::ADMIN_CODE) {
                $this->saveCache('abstract_website_id', $this->_storeManager->getStore()->getWebsiteId());
            } else {
                $this->saveCache('abstract_website_id', $this->_storeManager->getStore()->getWebsiteId());
            }
        }

        return $this->getCache('abstract_website_id');
    }
}
