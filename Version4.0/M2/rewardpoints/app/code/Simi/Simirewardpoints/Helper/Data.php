<?php

/**
 * SimirewardPoints Name and Image Helper
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Helper;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Data extends \Simi\Simirewardpoints\Helper\Config
{

    /**
     * @var Config
     */
    protected $helper;

    /**
     * @var Customer
     */
    protected $helperCustomer;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Simi\Simirewardpoints\Model\Rate
     */
    public $_rateModel;

    /**
     * @var Point
     */
    public $_helperPoint;

    const XML_PATH_ENABLE = 'simirewardpoints/general/enable';

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Config $globalConfig
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param Customer $customer
     * @param Point $helperPoint
     * @param \Simi\Simirewardpoints\Model\Rate $rateModel
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Simi\Simirewardpoints\Helper\Config $globalConfig,
        \Simi\Simirewardpoints\Helper\Customer $customer,
        \Simi\Simirewardpoints\Helper\Point $helperPoint,
        \Simi\Simirewardpoints\Model\Rate $rateModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->helper = $globalConfig;
        $this->helperCustomer = $customer;
        $this->_helperPoint = $helperPoint;
        $this->_rateModel = $rateModel;
        $this->_storeManager = $storeManager;
        $this->_priceCurrency = $priceCurrency;
        parent::__construct($context);
    }

    public function getUrlBuilder()
    {
        return $this->_urlBuilder;
    }

    /**
     * check reward points system is enabled
     *
     * @param mixed $store
     * @return boolean
     */
    public function isEnable($store = null)
    {
        return $this->helper->getConfig(self::XML_PATH_ENABLE, $store);
    }

    public function isEnableOutput()
    {
        return $this->isModuleOutputEnabled('Simi_Simirewardpoints');
    }

    public function isEnablePolicy($store = null)
    {
        return $this->helper->getConfig('simirewardpoints/general/show_policy_menu', $store);
    }

    public function getPolicyLink($store = null)
    {
        if (!$this->isEnablePolicy()) {
            return null;
        }

        return $this->_urlBuilder->getUrl('simirewardpoints/policy');
    }

    /**
     * get rewards points label to show on Account Navigation
     *
     * @return string
     */
    public function getMyRewardsLabel()
    {
        $pointAmount = $this->helperCustomer->getBalance();
        if ($pointAmount > 0) {
            $rate = $this->_rateModel->getRate(\Simi\Simirewardpoints\Model\Rate::POINT_TO_MONEY);
            if ($rate && $rate->getId()) {
                $baseAmount = $pointAmount * $rate->getMoney() / $rate->getPoints();
                $pointAmount = $this->convertAndFormat($baseAmount, true);
            } else {
                $pointAmount = $pointAmount . ' ' . (($pointAmount > 1) ? __('Points') : __('Point'));
            }
        }
        $imageHtml = $this->_helperPoint->getImageHtml(false);
        return __('My Rewards') . ' ' . $pointAmount . $imageHtml;
    }

    /**
     * @param $value
     * @param bool|true $format
     * @return mixed
     */
    public function convertPrice($value, $store = null)
    {

        if (!$store) {
            $store = $this->getStore();
        }
        return $this->_priceCurrency->convert($value, $store);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function convertAndFormat($value, $includeContainer = true)
    {
        return $this->_priceCurrency->convertAndFormat($value, $includeContainer);
    }

    /**
     * Get store view
     * @param null $storeId
     * @return int
     */
    public function getStore($storeId = null)
    {
        return $this->_storeManager->getStore($storeId);
    }
}
