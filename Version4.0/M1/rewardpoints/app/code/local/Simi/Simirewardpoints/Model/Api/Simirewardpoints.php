<?php

/**
 * Created by PhpStorm.
 * User: Max
 * Date: 5/3/16
 * Time: 9:37 PM
 */
class Simi_Simirewardpoints_Model_Api_Simirewardpoints extends Simi_Simiconnector_Model_Api_Abstract {

    protected $_DEFAULT_ORDER = 'customer_id';

    public function setBuilderQuery() {
        $data = $this->getData();
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if ($data['resourceid']) {
                $this->builderQuery = Mage::getModel('simirewardpoints/customer')->getCollection()->addFieldToFilter('customer_id', $customer->getId())->getFirstItem();
            } else {
                $this->builderQuery = Mage::getModel('simirewardpoints/customer')->getCollection()->addFieldToFilter('customer_id', $customer->getId());
            }
        } else
            throw new Exception(Mage::helper('customer')->__('Please login First.'), 4);
    }

    /*
     * Return Reward Points Information
     */

    public function show() {
        $data = $this->getData();
        if (($data['resourceid'] == 'home')||($data['resourceid'] == 'savesetting')) {
            $result = parent::show();
            $return = Mage::getModel('simirewardpoints/simiappmapping')->getRewardInfo();
            $result['simirewardpoint'] = array_merge($result['simirewardpoint'], $return);
        }
        return $result;
    }

    /*
     * Spend Point
     * and Save Setting
     */

    public function update() {
        $data = $this->getData();
        if ($data['resourceid'] == 'spend') {
            Mage::getModel('simirewardpoints/simiappmapping')->spendPoints($data);
            $onepageApi = Mage::getModel('simiconnector/api_orders');
            $data['resource'] = 'orders';
            $data['resourceid'] = 'onepage';
            $onepageApi->setData($data);
            $onepageApi->setBuilderQuery();
            $onepageApi->setPluralKey('orders');
            return $onepageApi->show();
        } else if ($data['resourceid'] == 'savesetting') {
            Mage::getModel('simirewardpoints/simiappmapping')->saveSettings($data);
            return $this->show();
        }
    }

}
