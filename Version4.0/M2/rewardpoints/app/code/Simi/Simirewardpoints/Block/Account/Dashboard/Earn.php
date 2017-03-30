<?php

/**
 * Simirewardpoints Account Dashboard Earning Policy
 *
 * @category    Simicart
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Block\Account\Dashboard;

class Earn extends \Magento\Framework\View\Element\Template
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

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Simi\Simirewardpoints\Model\Rate $modelRate,
        \Simi\Simirewardpoints\Helper\Data $helperData,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->_modelRate = $modelRate;
        $this->_helperData = $helperData;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * check showing container
     *
     * @return boolean
     */
    public function getCanShow()
    {

        $rate = $this->getEarningRate();
        if ($rate && $rate->getId()) {
            $canShow = true;
        } else {
            $canShow = false;
        }
        $container = new \Magento\Framework\DataObject([
            'can_show' => $canShow
        ]);
        $this->_eventManager->dispatch('simirewardpoints_block_dashboard_earn_can_show', [
            'container' => $container,
        ]);
        return $container->getCanShow();
    }

    /**
     * get earning rate
     *
     * @return /Simi/Simirewardpoints/Model/Rate
     */
    public function getEarningRate()
    {
        if (!$this->hasData('earning_rate')) {
            $this->setData('earning_rate', $this->_modelRate->getRate(\Simi\Simirewardpoints\Model\Rate::MONEY_TO_POINT));
        }
        return $this->getData('earning_rate');
    }

    /**
     * get current money formated of rate
     *
     * @param /Simi/Simirewardpoints/Model/Rate $rate
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
}
