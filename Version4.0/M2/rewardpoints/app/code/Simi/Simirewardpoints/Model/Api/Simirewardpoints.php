<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Simi\Simirewardpoints\Model\Api;

/**
 * Description of Simirewardpoints
 *
 * @author scott
 */
class Simirewardpoints extends \Simi\Simiconnector\Model\Api\Apiabstract {

    public $DEFAULT_ORDER = 'customer_id';

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {
        parent::__construct($simiObjectManager);
    }

    public function setBuilderQuery() {
        $data = $this->getData();
        $customerSession = $this->simiObjectManager
                ->get('Magento\Customer\Model\Session');
        if ($customerSession->isLoggedIn()) {
            $customer = $customerSession->getCustomer();
            $this->builderQuery = $this->simiObjectManager
                    ->get('Simi\Simirewardpoints\Model\Customer')
                    ->load($customer->getId(), 'customer_id');
        } else {
            throw new \Simi\Simiconnector\Helper\SimiException(__('Please login First.'), 4);
        }
    }

    public function show() {
        $data = $this->getData();
        if (($data['resourceid'] == 'home') || ($data['resourceid'] == 'savesetting')) {
            $result = parent::show();
            $return = $this->simiObjectManager
                    ->get('Simi\Simirewardpoints\Model\Simimapping')
                    ->getRewardInfo();
            $result['simirewardpoint'] = array_merge($result['simirewardpoint'], $return);
        }
        return $result;
    }
    
    public function update() {
        $data = $this->getData();
        if ($data['resourceid'] == 'spend') {
            $this->simiObjectManager
                    ->get('Simi\Simirewardpoints\Model\Simimapping')
                    ->spendPoints($data);
            $onepageApi = $this->simiObjectManager->get('Simi\Simiconnector\Model\Api\Orders');
            $data['resource'] = 'orders';
            $data['resourceid'] = 'onepage';
            $onepageApi->setData($data);
            $onepageApi->setBuilderQuery();
            $onepageApi->setPluralKey('orders');
            return $onepageApi->show();
        } else if ($data['resourceid'] == 'savesetting') {
            $this->simiObjectManager
                    ->get('Simi\Simirewardpoints\Model\Simimapping')->saveSettings($data);
            return $this->show();
        }
    }

}
