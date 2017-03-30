<?php

/**
 * Simirewardpoints show on customer account dashboard Block
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Block;

class Account extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Simi\Simirewardpoints\Helper\Policy
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    public $_objectManager;

    /**
     * @var \Simi\Simirewardpoints\Helper\Customer
     */
    protected $_helperCustomer;

    /**
     * @var \Simi\Simirewardpoints\Helper\Point
     */
    protected $_helperPoint;

    /**
     * @var \Simi\Simirewardpoints\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Simi\Simirewardpoints\Model\Rate
     */
    protected $_modelRate;

    /**
     * Account constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Simi\Simirewardpoints\Helper\Policy $globalConfig
     * @param \Simi\Simirewardpoints\Helper\Customer $helperCustomer
     * @param \Simi\Simirewardpoints\Helper\Point $helperPoint
     * @param \Simi\Simirewardpoints\Helper\Data $helperData
     * @param \Simi\Simirewardpoints\Model\Rate $modelRate
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Simi\Simirewardpoints\Helper\Policy $globalConfig,
        \Simi\Simirewardpoints\Helper\Customer $helperCustomer,
        \Simi\Simirewardpoints\Helper\Point $helperPoint,
        \Simi\Simirewardpoints\Helper\Data $helperData,
        \Simi\Simirewardpoints\Model\Rate $modelRate
    ) {

        parent::__construct($context, []);
        $this->helper = $globalConfig;
        $this->_helperCustomer = $helperCustomer;
        $this->_helperPoint = $helperPoint;
        $this->_helperData = $helperData;
        $this->_modelRate = $modelRate;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * get current balance of customer as text
     *
     * @return string
     */
    public function getBalanceText()
    {
        return $this->_helperCustomer->getBalanceFormated();
    }

    /**
     * get holding balance of customer as text
     *
     * @return int
     */
    public function getHoldingBalance()
    {
        $holdingBalance = $this->_helperCustomer->getAccount()->getHoldingBalance();
        if ($holdingBalance > 0) {
            return $this->_helperPoint->format($holdingBalance);
        }
        return '';
    }

    /**
     * get point money balance of customer
     *
     * @return string
     */
    public function getPointMoney()
    {
        $pointAmount = $this->_helperCustomer->getBalance();
        if ($pointAmount > 0) {
            $rate = $this->_modelRate->getRate(\Simi\Simirewardpoints\Model\Rate::POINT_TO_MONEY);
            if ($rate && $rate->getId()) {
                $baseAmount = $pointAmount * $rate->getMoney() / $rate->getPoints();
                return $this->_helperData->convertAndFormat($baseAmount, true);
            }
        }
        return '';
    }

    public function getImageHtml($hasAnchor = false)
    {
        return $this->_helperPoint->getImageHtml($hasAnchor);
    }
}
