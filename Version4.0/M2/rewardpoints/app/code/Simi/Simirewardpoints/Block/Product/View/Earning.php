<?php

namespace Simi\Simirewardpoints\Block\Product\View;

class Earning extends \Simi\Simirewardpoints\Block\RewardpointTemplate
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Simi\Simirewardpoints\Helper\Point
     */
    protected $_helperPoint;

    /**
     * @var \Simi\Simirewardpoints\Helper\Calculation\Earning
     */
    protected $_calculationEarning;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    public $_objectManager;

    /**
     * Earning constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Simi\Simirewardpoints\Helper\Calculation\Earning $calculationEarning
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Simi\Simirewardpoints\Helper\Point $helperPoint,
        \Simi\Simirewardpoints\Helper\Calculation\Earning $calculationEarning,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->_helperPoint = $helperPoint;
        $this->_calculationEarning = $calculationEarning;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Check store is enable for display on minicart sidebar
     *
     * @return boolean
     */
    public function enableDisplay()
    {
        $enableDisplay = $this->_helperPoint->showOnProduct();
        $container = new \Magento\Framework\DataObject([
            'enable_display' => $enableDisplay
        ]);

        $this->_eventManager->dispatch('simirewardpoints_block_show_earning_on_product', [
            'container' => $container,
        ]);

        if ($container->getEnableDisplay() && !$this->hasEarningRate() || $this->_coreRegistry->registry('product')->getSimiRewardpointsSpend()) {
            return false;
        }
        return $container->getEnableDisplay();
    }

    /**
     * check product can earn point by rate or not
     *
     * @return boolean
     */
    public function hasEarningRate()
    {

        if ($product = $this->_coreRegistry->registry('product')) {
            if (!$this->_calculationEarning->getRateEarningPoints(10000)) {
                return false;
            }
            $productPrice = $product->getFinalPrice();

            if ($productPrice < 0.0001 && $product->getTypeId() == 'bundle') {
                $totalsprice = $product->getPriceModel()->getTotalPrices($product);
                if (isset($totalsprice[0]) && $totalsprice[0]) {
                    $productPrice = $totalsprice[0];
                }
            }
            if ($productPrice > 0.0001) {
                return true;
            }
        }
        return false;
    }

    /**
     * get Image (HTML) for reward points
     *
     * @param boolean $hasAnchor
     * @return string
     */
    public function getImageHtml($hasAnchor = true)
    {

        return $this->_helperPoint->getImageHtml($hasAnchor);
    }

    /**
     * get plural points name
     *
     * @return string
     */
    public function getPluralPointName()
    {

        return $this->_helperPoint->getPluralName();
    }

    public function getEarningPoints()
    {
        if ($this->hasData('earning_points')) {
            return $this->getData('earning_points');
        }
        if ($this->_coreRegistry->registry('product') && $point = $this->_calculationEarning->getRateEarningPoints($this->_coreRegistry->registry('product')->getFinalPrice())) {
            $this->setData('earning_points', $point);
        }
        return $this->getData('earning_points');
    }
}
