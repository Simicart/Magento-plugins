<?php

/**
 * Simirewardpoints Account Dashboard
 *
 * @category    SimiCart
 * @package     Simi_Simirewardpoints
 * @author      SimiCart Developer
 */

namespace Simi\Simirewardpoints\Block\Account;

class Dashboard extends \Magento\Framework\View\Element\Template
{

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
     * @var \Simi\Simirewardpoints\Model\Rate
     */
    protected $_modelRate;

    /**
     * @var \Simi\Simirewardpoints\Helper\Data
     */
    protected $_helperData;

    /**
     * Dashboard constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Simi\Simirewardpoints\Helper\Customer $helperCustomer
     * @param \Simi\Simirewardpoints\Helper\Point $helperPoint
     * @param \Simi\Simirewardpoints\Model\Rate $modelRate
     * @param \Simi\Simirewardpoints\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Simi\Simirewardpoints\Helper\Customer $helperCustomer,
        \Simi\Simirewardpoints\Helper\Point $helperPoint,
        \Simi\Simirewardpoints\Model\Rate $modelRate,
        \Simi\Simirewardpoints\Helper\Data $helperData
    ) {

        parent::__construct($context, []);
        $this->_helperCustomer = $helperCustomer;
        $this->_helperPoint = $helperPoint;
        $this->_modelRate = $modelRate;
        $this->_helperData = $helperData;
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
}
