<?php

/**
 * 
 */
class Simi_Simitracking_Model_Api_Addresses extends Simi_Simiconnector_Model_Api_Addresses {

    public function setBuilderQuery() {
        $data = $this->getData();
        if ($data['resourceid']) {
            
        } else {
            if (!isset($data['params']) || (!isset($data['params']['customer_id']))) {
                throw new Exception($this->_helper->__('Please send the Customer ID'), 4);
            } else {
                Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::CUSTOMER_ADDRESS_LIST);
                $customer = Mage::getModel('customer/customer')->load($data['params']['customer_id']);
                $addressArray = array();
                $billing = $customer->getPrimaryBillingAddress();
                if ($billing) {
                    $addressArray[] = $billing->getId();
                }
                $shipping = $customer->getPrimaryShippingAddress();
                if ($shipping) {
                    $addressArray[] = $shipping->getId();
                }
                foreach ($customer->getAddresses() as $index => $address) {
                    $addressArray[] = $index;
                }
                $this->builderQuery = Mage::getModel('customer/address')->getCollection()
                        ->addFieldToFilter('entity_id', array('in' => $addressArray));
            }
        }
    }


        
    /*
     * View Address List
     */

    public function index() {
        $result = parent::index();
        $addresses = $result['addresses'];
        foreach ($addresses as $index => $address) {
            $addressModel = Mage::getModel('customer/address')->load($address['entity_id']);
            $addresses[$index] = array_merge($address, Mage::helper('simiconnector/address')->getAddressDetail($addressModel, null));
        }
        $result['addresses'] = $addresses;
        return $result;
    }


}
