<?php

/**
 * RewardPoints Show Cart Total (Review about Earning/Spending Reward Points) on Backend
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Block\Adminhtml\Cart;

class Label extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{

    protected $_template = 'simirewardpoints/checkout/cart/label.phtml';
    public $_objectManager;

    public function _construct()
    {
        parent::_construct();
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * check reward points system is enabled or not
     *
     * @return boolean
     */
    public function isEnable()
    {
        return $this->_objectManager->create('Simi\Simirewardpoints\Helper\Data')->isEnable();
    }

    /**
     * get reward points helper
     *
     * @return \Simi\Simirewardpoints\Helper\Point
     */
    public function getPointHelper()
    {
        return $this->_objectManager->create('Simi\Simirewardpoints\Helper\Point');
    }

    /**
     * get total points that customer use to spend for order
     *
     * @return int
     */
    public function getSpendingPoint()
    {
        return $this->_objectManager->create('Simi\Simirewardpoints\Helper\Calculation\Spending')->getTotalPointSpent();
    }

    /**
     * get total points that customer can earned by purchase order
     *
     * @return int
     */
    public function getEarningPoint()
    {
        return $this->_objectManager->create('Simi\Simirewardpoints\Helper\Calculation\Earning')->getTotalPointsEarning();
    }
}
