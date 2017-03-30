<?php

/**
 * Simirewardpoints Account Dashboard Earning Policy
 *
 * @category    Simicart
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Block\Account\Dashboard;

class Spend extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    public $_objectManager;

    /**
     * @var \Simi\Simirewardpoints\Model\Rate
     */
    protected $_modelRate;

    /**
     * @var \Simi\Simirewardpoints\Helper\Data
     */
    protected $_helperData;

    /**
     * Spend constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Simi\Simirewardpoints\Model\Rate $modelRate
     * @param \Simi\Simirewardpoints\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Simi\Simirewardpoints\Model\Rate $modelRate,
        \Simi\Simirewardpoints\Helper\Data $helperData,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_modelRate = $modelRate;
        $this->_helperData = $helperData;
    }

    /**
     * check showing container
     *
     * @return boolean
     */
    public function getCanShow()
    {
        $rate = $this->getSpendingRate();
        if ($rate && $rate->getId()) {
            $canShow = true;
        } else {
            $canShow = false;
        }
        $container = new \Magento\Framework\DataObject([
            'can_show' => $canShow
        ]);
        $this->_eventManager->dispatch('simirewardpoints_block_dashboard_spend_can_show', [
            'container' => $container,
        ]);
        return $container->getCanShow();
    }

    /**
     * get spending rate
     *
     * @return \Simi\Simirewardpoints\Model\Rate
     */
    public function getSpendingRate()
    {
        if (!$this->hasData('spending_rate')) {
            $this->setData('spending_rate', $this->_modelRate->getRate(\Simi\Simirewardpoints\Model\Rate::POINT_TO_MONEY));
        }
        return $this->getData('spending_rate');
    }

    /**
     * get current money formated of rate
     *
     * @param \Simi\Simirewardpoints\Model\Rate $rate
     * @return string
     */
    public function getCurrentMoney($rate)
    {
        if ($rate && $rate->getId()) {
            $money = $rate->getMoney();
            return $this->_helperData->convertAndFormat($money, true);
        }
        return '';
    }

    public function getRewardPolicyLink()
    {
        $link = '<a href="' . $this->_helperData->getPolicyLink() . '" class="rewardpoints-title-link">' . __('Reward Policy') . '</a>';
        return $link;
    }
}
