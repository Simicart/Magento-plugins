<?php

/**
 * RewardPoints Show Spending Point on Shopping Cart Page
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Block\Checkout\MiniCart;

class Content extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Simi\Simirewardpoints\Helper\Point
     */
    protected $helperPoint;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;
    protected $_customerSession;
    protected $_calculationEarning;
    
    /**
     * Content constructor.
     * @param \Simi\Simirewardpoints\Helper\Point $helperPoint
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Simi\Simirewardpoints\Helper\Calculation\Earning $calculationEarning
     * @param array $data
     */
    public function __construct(
        \Simi\Simirewardpoints\Helper\Point $helperPoint,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Simi\Simirewardpoints\Helper\Calculation\Earning $calculationEarning,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->helperPoint = $helperPoint;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_customerSession = $customerSession;
        $this->_calculationEarning = $calculationEarning;
    }

    /**
     * Check store is enable for display on minicart sidebar
     *
     * @return type
     */
    public function enableDisplay()
    {
        return $this->helperPoint->showOnMiniCart();
    }

    /**
     * get Image (HTML) for reward points
     *
     * @param boolean $hasAnchor
     * @return string
     */
    public function getImageHtml($hasAnchor = true)
    {
        return $this->helperPoint->getImageHtml($hasAnchor);
    }

    /**
     * @return array
     */
    public function knockoutData()
    {
        $earning = $this->_calculationEarning;
        $results = [];
        if ($this->enableDisplay() && $earningPoint = $earning->getTotalPointsEarning()) {
            $results['enableReward'] = $this->enableDisplay();
            $results['getImageHtml'] = $this->getImageHtml(true);
            $results['customerLogin'] = $this->_customerSession->isLoggedIn();
            $results['earnPoint'] = $this->helperPoint->format($earningPoint);
            $results['urlRedirectLogin'] = $this->_urlBuilder->getUrl('simirewardpoints/index/redirectLogin', [
                'redirect' => $this->_urlBuilder->getCurrentUrl()
                    ]);
        }

        return $results;
    }
}
