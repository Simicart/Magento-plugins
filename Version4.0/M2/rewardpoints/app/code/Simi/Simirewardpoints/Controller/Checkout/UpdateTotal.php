<?php

/**
 * @category Simi
 * @package  Simi_Simirewardpoints
 * @module   Simirewardpoints
 * @author   Simicart Developer
 */

namespace Simi\Simirewardpoints\Controller\Checkout;

class UpdateTotal extends \Simi\Simirewardpoints\Controller\AbstractAction
{

    /**
     * @return mixed
     */
    public function execute()
    {
        $this->_checkoutSession->setData('use_point', true);
        $this->_checkoutSession->setRewardSalesRules([
            'rule_id' => $this->getRequest()->getPostValue()['simireward_sales_rule'],
            'use_point' => $this->getRequest()->getPostValue()['simireward_sales_point'],
        ]);
        if ($this->_checkoutCart->getQuote()->getItemsCount()) {
//            $cart->init();
            $this->_checkoutCart->save();
            $this->checkUseDefault();
        }
        $this->_checkoutSession->getQuote()->collectTotals()->save();
        $amount = $this->_checkoutCart->getQuote()->getSimirewardpointsBaseDiscount();
        $result = [
            'earning' => $this->_helperPoint->format($this->_checkoutForm->getSimiEarningPoint()),
            'spending' => $this->_helperPoint->format($this->_checkoutForm->getSimiSpendingPoint()),
            'usePoint' => strip_tags($this->_helperData->convertAndFormat(-$amount)),
        ];
        return $this->getResponse()->setBody(\Zend_Json::encode($result));
    }

    public function checkUseDefault()
    {
        $this->_checkoutSession->setData('use_max', 0);
        $rewardSalesRules = $this->_checkoutSession->getSimiRewardSalesRules();
        $arrayRules = $this->_helperSpend->getRulesArray();
        if ($this->_calculationSpending->isUseMaxPointsDefault()) {
            if (isset($rewardSalesRules['use_point']) &&
                    isset($rewardSalesRules['rule_id']) &&
                    isset($arrayRules[$rewardSalesRules['rule_id']]) &&
                    isset($arrayRules[$rewardSalesRules['rule_id']]['sliderOption']) &&
                    isset($arrayRules[$rewardSalesRules['rule_id']]['sliderOption']['maxPoints']) && ($rewardSalesRules['use_point'] < $arrayRules[$rewardSalesRules['rule_id']]['sliderOption']['maxPoints'])) {
                $this->_checkoutSession->setData('use_max', 0);
            } else {
                $this->_checkoutSession->setData('use_max', 1);
            }
        }
    }
}
