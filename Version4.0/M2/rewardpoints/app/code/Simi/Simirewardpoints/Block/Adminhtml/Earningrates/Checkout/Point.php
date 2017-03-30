<?php

namespace Simi\Simirewardpoints\Block\Adminhtml\Earningrates\Checkout;

/**
 * Adminhtml sales order create shipping method form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Point extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{

    /**
     * @var
     */
    protected $_objectManager;

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_objectManager = $objectManager;
        $session = $this->_objectManager->create('Magento\Checkout\Model\Session');
        $session->setData('use_point', true);
        $session->setRewardSalesRules([
            'rule_id' => $this->getRequest()->getPostValue()['reward_sales_rule'],
            'use_point' => $this->getRequest()->getPostValue()['reward_sales_point'],
        ]);
//        $this->checkUseDefault();
        parent::_construct();
        $this->setId('sales_order_create_simirewardpoints');
    }

    public function checkUseDefault()
    {

        $session = $this->_objectManager->create('Magento\Checkout\Model\Session');
        $session->setData('use_max', 0);
        $rewardSalesRules = $session->getRewardSalesRules();
        $arrayRules = $this->_objectManager->create('Simi\Simirewardpoints\Helper\Block\Spend')->getRulesArray();
        if ($this->_objectManager->create('Simi\Simirewardpoints\Helper\Calculation\Spending')->isUseMaxPointsDefault()) {
            if (isset($rewardSalesRules['use_point']) &&
                    isset($rewardSalesRules['rule_id']) &&
                    isset($arrayRules[$rewardSalesRules['rule_id']]) &&
                    isset($arrayRules[$rewardSalesRules['rule_id']]['sliderOption']) &&
                    isset($arrayRules[$rewardSalesRules['rule_id']]['sliderOption']['maxPoints']) && ($rewardSalesRules['use_point'] < $arrayRules[$rewardSalesRules['rule_id']]['sliderOption']['maxPoints'])) {
                $session->setData('use_max', 0);
            } else {
                $session->setData('use_max', 1);
            }
        }
    }
}
