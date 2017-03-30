<?php

/**
 * Simirewardpoints Account Dashboard Earning Policy
 *
 * @category    Simicart
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Block\Account\Dashboard;

class Policy extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    public $_objectManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $_storeManager;

    /**
     * @var \Simi\Simirewardpoints\Helper\Config
     */
    protected $_helperConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Simi\Simirewardpoints\Helper\Config $helperConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $this->_helperConfig = $helperConfig;
    }

    /**
     * earning transaction will be expired after days
     *
     * @return int
     */
    public function getTransactionExpireDays()
    {
        $days = (int) $this->_helperConfig->getConfig(\Simi\Simirewardpoints\Helper\Calculation\Earning::XML_PATH_EARNING_EXPIRE);
        return max(0, $days);
    }

    /**
     * get day holling point
     *
     * @return int
     */
    public function getHoldingDays()
    {
        $days = (int) $this->_helperConfig->getConfig(\Simi\Simirewardpoints\Helper\Calculation\Earning::XML_PATH_HOLDING_DAYS);
        return max(0, $days);
    }

    /**
     * Maximum point balance allowed
     *
     * @return int
     */
    public function getMaxPointBalance()
    {
        $maxBalance = (int) $this->_helperConfig->getConfig(\Simi\Simirewardpoints\Model\Transaction::XML_PATH_MAX_BALANCE);
        return max(0, $maxBalance);
    }

    /**
     * Minimum point allowed to redeem
     *
     * @return int
     */
    public function getRedeemablePoints()
    {
        $points = (int) $this->_helperConfig->getConfig(\Simi\Simirewardpoints\Helper\Customer::XML_PATH_REDEEMABLE_POINTS);
        return max(0, $points);
    }

    /**
     * Maximun point spneding per order
     *
     * @return int
     */
    public function getMaxPerOrder()
    {
        $points = (int) $this->_objectManager->create('Simi\Simirewardpoints\Helper\Config')->getConfig(
            \Simi\Simirewardpoints\Helper\Calculation\Spending::XML_PATH_MAX_POINTS_PER_ORDER
        );
        return max(0, $points);
    }
}
