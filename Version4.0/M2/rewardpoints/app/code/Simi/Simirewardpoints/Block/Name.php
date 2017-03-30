<?php

/**
 * RewardPoints Name and Image Block
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Block;

class Name extends \Magento\Framework\View\Element\Template
{

    public $_objectManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context
    ) {
        parent::__construct($context, []);
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->setTemplate('Simi_Simirewardpoints::simirewardpoints/name.phtml');
    }

    protected function _toHtml()
    {
        return parent::_toHtml();
    }

    /**
     * get current balance of customer as text
     *
     * @return string
     */
    public function getBalanceText()
    {
        return $this->_objectManager->create('Simi\Simirewardpoints\Helper\Customer')->getBalanceFormated();
    }

    /**
     * get Image (Logo) HTML for reward points
     *
     * @return string
     */
    public function getImageHtml()
    {
        return $this->_objectManager->create('Simi\Simirewardpoints\Helper\Point')->getImageHtml($this->getIsAnchorMode());
    }
}
