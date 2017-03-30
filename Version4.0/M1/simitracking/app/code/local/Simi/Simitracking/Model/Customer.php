<?php

/**
 * 
 */
class Simi_Simitracking_Model_Customer extends Simi_Simiconnector_Model_Customer {

    protected function _helperCustomer() {
        return Mage::helper('simiconnector/customer');
    }

    public function newCustomer($data) {
        $data = $data['contents'];
        $checkCustomer = $this->getCustomerByEmail($data->email);
        if ($checkCustomer->getId()) {
            throw new Exception($this->_helperCustomer()->__('Account is already exist'), 4);
        }
        $customer = $this->_createCustomer($data);
        $result = array();
        $result['user_id'] = $customer->getId();
        return $customer;
    }

    public function updateCustomer($data, $customer) {
        $data = $data['contents'];
        $newPass = $data->new_password;
        $confPass = $data->com_password;

        $customerData = array(
            'firstname' => $data->firstname,
            'lastname' => $data->lastname,
            'email' => $data->email,
        );

        $fields = Mage::getConfig()->getFieldset('customer_account');
        foreach ($fields as $code => $node) {
            if ($node->is('update') && isset($customerData[$code])) {
                $customer->setData($code, $customerData[$code]);
            }
        }

        if ($data->change_password == 1) {
            $customer->setChangePassword(1);
            $customer->setPassword($newPass);
            $customer->setConfirmation($confPass);
            $customer->setPasswordConfirmation($confPass);
        }

        $this->updateData($customer, $data);

        $customerErrors = $customer->validate();
        if (is_array($customerErrors))
            throw new Exception($this->_helperCustomer()->__('Invalid profile information'), 4);
        $customer->setConfirmation(null);
        $customer->save();
        return $customer;
    }

    private function _createCustomer($data) {
        $customer = Mage::getModel('customer/customer')
                ->setFirstname($data->firstname)
                ->setLastname($data->lastname)
                ->setEmail($data->email);
        $this->updateData($customer, $data);
        if (!$data->password)
            $data->password = $customer->generatePassword();
        $customer->setPassword($data->password);
        $customer->save();
        $customer->setConfirmation(null);
        $customer->save();
        return $customer;
    }

    public function updateData($customer, $data) {
        if (isset($data->day) && $data->day != "") {
            $birthday = $data->year . "-" . $data->month . "-" . $data->day;
            $customer->setDob($birthday);
        }
        if (isset($data->taxvat)) {
            $customer->setTaxvat($data->taxvat);
        }
        if (isset($data->gender) && $data->gender) {
            $customer->setGender($data->gender);
        }
        if (isset($data->prefix) && $data->prefix) {
            $customer->setPrefix($data->prefix);
        }
        if (isset($data->middlename) && $data->middlename) {
            $customer->setMiddlename($data->middlename);
        }
        if (isset($data->suffix) && $data->suffix) {
            $customer->setSuffix($data->suffix);
        }
    }
    
    public function loginWithKeySession($data) {
        // required $data['params']['qr_session_id']
        // required $data['params']['new_token_id']
        if(!isset($data['params']['qr_session_id']) || !isset($data['params']['new_token_id']))
            return false;
        return Mage::helper('simitracking')->continueSessionWithSessionId($data['params']['qr_session_id']);
    }

}
