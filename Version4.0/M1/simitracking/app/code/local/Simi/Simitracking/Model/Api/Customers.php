<?php

/**
 * 
 */
class Simi_Simitracking_Model_Api_Customers extends Simi_Simiconnector_Model_Api_Customers {

    protected $_DEFAULT_ORDER = 'entity_id';
    protected $_RETURN_MESSAGE;

    public function setBuilderQuery() {
        $data = $this->getData();
        if ($data['resourceid']) {
            $this->builderQuery = Mage::getModel('customer/customer')->load($data['resourceid']);
        } else {
            Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::CUSTOMER_LIST);
            $this->builderQuery = Mage::getModel('customer/customer')->getCollection();
            $this->builderQuery->addAttributeToSelect(array('prefix', 'firstname', 'middlename', 'lastname', 'suffix'));
        }
    }

    /*
     * New Customer
     */

    public function store() {
        Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::CUSTOMER_EDIT);
        $data = $this->getData();
        $customer = Mage::getModel('simitracking/customer')->newCustomer($data);
        $this->builderQuery = $customer;
        $this->_RETURN_MESSAGE = Mage::helper('customer')->__("Customer Created");
        return $this->show();
    }

    /*
     * Update Customer
     */

    public function update() {
        Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::CUSTOMER_EDIT);
        $data = $this->getData();
        $customer = Mage::getModel('simitracking/customer')->updateCustomer($data, $this->builderQuery);
        $this->builderQuery = $customer;
        $this->_RETURN_MESSAGE = Mage::helper('customer')->__('The account information has been saved.');
        return $this->show();
    }

    /*
     * Show customer Detail
     */

    public function show() {
        Mage::helper('simitracking')->isAllowed(Simi_Simitracking_Helper_Data::CUSTOMER_DETAIL);
        $entity = $this->builderQuery;
        $data = $this->getData();
        $parameters = $data['params'];
        $fields = array();
        if (isset($parameters['fields']) && $parameters['fields']) {
            $fields = explode(',', $parameters['fields']);
        }
        $info = $entity->toArray($fields);
        if (isset($info['default_billing'])) 
            $info['billing_address_data'] = Mage::helper('simiconnector/address')->getAddressDetail(Mage::getModel('customer/address')->load($info['default_billing']));
        if (isset($info['default_shipping'])) 
            $info['shipping_address_data'] = Mage::helper('simiconnector/address')->getAddressDetail(Mage::getModel('customer/address')->load($info['default_shipping']));
        if (isset($info['gender'])) {
            $info['gender'] = $entity->getResource()->getAttribute('gender')->getSource()->getOptionText($entity->getGender());
        }
        if (isset($info['dob'])) {
            $dobArray = explode(" ", $info['dob']);
            $info['dob'] = $dobArray[0];
        }
        
        return $this->getDetail($info);
    }

    /*
     * Prevent Auto Login
     */
    protected function renewCustomerSesssion($data) {
        return;
    }

}
