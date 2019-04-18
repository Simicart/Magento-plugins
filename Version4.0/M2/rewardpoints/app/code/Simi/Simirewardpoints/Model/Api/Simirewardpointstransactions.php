<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Simi\Simirewardpoints\Model\Api;

/**
 * Description of Simirewardpointstransactions
 *
 * @author scott
 */
class Simirewardpointstransactions extends \Simi\Simiconnector\Model\Api\Apiabstract{
   const DEFAULT_DIR   = 'DESC';
   public $DEFAULT_ORDER = 'created_time';
    //put your code here
    public function setBuilderQuery() {
        $data = $this->getData();
        $customerSession = $this->simiObjectManager
                ->get('Magento\Customer\Model\Session');
        if ($customerSession->isLoggedIn()) {
            if ($data['resourceid']) {
                return;
            } else {
                $this->builderQuery = $this->simiObjectManager
                                ->get('Simi\Simirewardpoints\Model\Simimapping')->getHistory();
            }
        } else {
            throw new \Simi\Simiconnector\Helper\SimiException(__('Please login First.'), 4);
        }
    }
    
    public function index() {
        $result = parent::index();
        $statuses = array(
            \Simi\Simirewardpoints\Model\Transaction::STATUS_PENDING => 'pending',
            \Simi\Simirewardpoints\Model\Transaction::STATUS_ON_HOLD => 'onhold',
            \Simi\Simirewardpoints\Model\Transaction::STATUS_COMPLETED => 'completed',
            \Simi\Simirewardpoints\Model\Transaction::STATUS_CANCELED => 'canceled',
            \Simi\Simirewardpoints\Model\Transaction::STATUS_EXPIRED => 'expired'
        );
        $helper = $this->simiObjectManager->create('\Simi\Simirewardpoints\Helper\Point');
        foreach ($result['simirewardpointstransactions'] as $index=>$transactionInfo) {
            $transaction = $this->simiObjectManager
                    ->get('\Simi\Simirewardpoints\Model\Transaction')
                    ->load($transactionInfo['transaction_id']);
            $title = $transaction->getTitle();
            if ($title == '') {
                $title = $transaction->getData('action');
            }

            $transactionInfo = array_merge($transactionInfo, array(
                'title' => $title,
                'point_amount' => (int) $transaction->getData('point_amount'),
                'point_label' => $helper->format($transaction->getData('point_amount')),
                'created_time' => $transaction->getData('created_time'),
                'expiration_date' => $transaction->getData('expiration_date') ? $transaction->getData('expiration_date') : '',
                'status' => $statuses[$transaction->getData('status')]
            ));

            $result['simirewardpointstransactions'][$index] = $transactionInfo;
        }
        return $result;
    }
}
