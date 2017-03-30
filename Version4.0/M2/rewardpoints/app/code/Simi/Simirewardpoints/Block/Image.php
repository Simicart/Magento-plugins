<?php

/**
 * SimirewardPoints Image Block
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      SimiCart Developer
 */

namespace Simi\Simirewardpoints\Block;

class Image extends \Magento\Framework\View\Element\Template
{

    protected $_rewardPointsHtml = null;
    protected $_rewardAnchorHtml = null;

    /**
     * @var \Simi\Simirewardpoints\Helper\Policy
     */
    protected $helper;

    /**
     * @var \Simi\Simirewardpoints\Helper\Point
     */
    protected $_helperPoint;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Simi\Simirewardpoints\Helper\Point $helperPoint,
        \Simi\Simirewardpoints\Helper\Policy $globalConfig
    ) {

        parent::__construct($context, []);
        $this->helper = $globalConfig;
        $this->_helperPoint = $helperPoint;
    }

    /**
     * prepare block's layout
     *
     * @return \Simi\Simirewardpoints\Block\Image
     */
    public function _prepareLayout()
    {
        $this->setTemplate('Simi_Simirewardpoints::simirewardpoints/image.phtml');
        return parent::_prepareLayout();
    }

    /**
     * Render block HTML, depend on mode of name showed
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getIsAnchorMode()) {
            if (is_null($this->_rewardAnchorHtml)) {
                $html = parent::_toHtml();
                if ($html) {
                    $this->_rewardAnchorHtml = $html;
                } else {
                    $this->_rewardAnchorHtml = '';
                }
            }
            return $this->_rewardAnchorHtml;
        } else {
            if (is_null($this->_rewardPointsHtml)) {
                $html = parent::_toHtml();
                if ($html) {
                    $this->_rewardPointsHtml = $html;
                } else {
                    $this->_rewardPointsHtml = '';
                }
            }
            return $this->_rewardPointsHtml;
        }
    }

    /**
     * get Policy Link for reward points system
     *
     * @return string
     */
    public function getPolicyUrl()
    {
        return $this->helper->getPolicyUrl();
    }

    public function getPointImage()
    {
        return $this->_helperPoint->getImage();
    }
}
