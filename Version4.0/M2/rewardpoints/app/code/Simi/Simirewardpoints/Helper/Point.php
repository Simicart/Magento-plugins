<?php

/**
 * SimirewardPoints Name and Image Helper
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Helper;

class Point extends \Simi\Simirewardpoints\Helper\Config
{

    const XML_PATH_POINT_NAME = 'simirewardpoints/general/point_name';
    const XML_PATH_POINT_NAME_PLURAL = 'simirewardpoints/general/point_names';
    const XML_PATH_POINT_IMAGE = 'simirewardpoints/general/point_image';
    const XML_PATH_DISPLAY_PRODUCT = 'simirewardpoints/display/product';
    const XML_PATH_DISPLAY_MINICART = 'simirewardpoints/display/minicart';

    /**
     * @var Config
     */
    protected $helper;

    /**
     * @var \Simi\Simirewardpoints\Block\Image
     */
    protected $_elementTemplate;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Simi\Simirewardpoints\Helper\Config $globalConfig,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\View\Element\Template $elementTemplate,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->helper = $globalConfig;
        $this->_elementTemplate = $elementTemplate;
        $this->_layout = $layout;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * get Label for Point, default is "Point"
     *
     * @param mixed $store
     * @return string
     */
    public function getName($store = null)
    {
        if ($pointName = trim($this->helper->getConfig(self::XML_PATH_POINT_NAME, $store))) {
            return $pointName;
        }
        return __('Point');
    }

    /**
     * get reward Label for Points (plural), default is "Points"
     *
     * @param mixed $store
     * @return string
     */
    public function getPluralName($store = null)
    {
        if ($pluralName = trim($this->helper->getConfig(self::XML_PATH_POINT_NAME_PLURAL, $store))) {
            return $pluralName;
        }
        return __('Points');
    }

    /**
     * get point image on store, default is template image url
     *
     * @param mixed $store
     * @return string image url
     */
    public function getImage($store = null)
    {
        if ($imgPath = trim($this->helper->getConfig(self::XML_PATH_POINT_IMAGE, $store))) {
            return $this->_storeManager->getStore()->getBaseUrl('media') . 'rewardpoints/' . $imgPath;
        }

        return $this->_elementTemplate->getViewFileUrl('Simi_Simirewardpoints::images/simirewardpoints/point.png');
    }

    /**
     * get Image (by HTML code)
     *
     * @param boolean $hasAnchor
     * @return string
     */
    public function getImageHtml($hasAnchor = false)
    {

        return $this->_layout->getBlockSingleton('\Simi\Simirewardpoints\Block\Image')
                        ->setIsAnchorMode($hasAnchor)
                        ->toHtml();
    }

    /**
     * format point with unit (name). Ex: 1 Point, 2 Points
     *
     * @param int $points
     * @param mixed $store
     * @return string
     */
    public function format($points, $store = null)
    {
        $points = intval($points);
        if (abs($points) <= 1) {
            return $points . ' ' . $this->getName($store);
        }
        return $points . ' ' . $this->getPluralName($store);
    }

    /**
     * check show earning reward points on top link
     *
     * @param type $store
     * @return string
     */
    public function showOnProduct($store = null)
    {
        return $this->helper->getConfig(self::XML_PATH_DISPLAY_PRODUCT, $store);
    }

    /**
     * check show earning reward points on mini cart
     *
     * @param type $store
     * @return string
     */
    public function showOnMiniCart($store = null)
    {
        return $this->helper->getConfig(self::XML_PATH_DISPLAY_MINICART, $store);
    }
}
