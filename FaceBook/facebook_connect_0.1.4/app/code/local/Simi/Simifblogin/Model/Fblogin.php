<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Simifblogin
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Simifblogin Model
 * 
 * @category    Magestore
 * @package     Magestore_Simifblogin
 * @author      Magestore Developer
 */
class Simi_Simifblogin_Model_Fblogin extends Simi_Connector_Model_Abstract {
    
    public $_pass = "";
    public function _getSession(){
        return Mage::getSingleton('customer/session');
    }
    public function login($data) {  
        // Zend_debug::dump($data);die();
        if ((int) Mage::getStoreConfig("simifblogin/general/enable") == 0) {
            return Mage::getSingleton('core/session')->addError(Mage::helper('simifblogin')->__('Extension was disable'));
        }
        $email = $data->email;
        $name = $data->name;        
        $customer_name = Mage::helper('connector/checkout')->soptName($name);       
        $customer_name['email'] = $email;
        $store_id = Mage::app()->getStore()->getStoreId();
        $website_id = Mage::app()->getStore()->getWebsiteId();
        $customer = $this->getCustomerByEmail($email, $website_id);     
        if (!$customer || !$customer->getId()) {
            $customer = $this->createCustomerMultiWebsite($customer_name, $website_id, $store_id);
            // if (!is_null($customer)) {
            //     return $this->statusError($customer);
            // }

            // $customer = Mage::getSingleton('customer/session')->getCustomer();
        }else{
            if($name != $customer->getName()){
                $this->editProfile($customer_name, $customer);
            }           
        }

        if ($customer->getConfirmation()) {
            try {
                $customer->setConfirmation(null);
                $customer->save();
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('simifblogin')->__('Error'));
            }
        }

        Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
        
        $information = $this->statusSuccess();
        $information['message'] = array(Mage::helper('simifblogin')->__('Login Success'));
        $result = array();
        $result['user_id'] = $customer->getId();
        $result['user_email'] = $customer->getEmail();
        $result['user_password'] = $customer->getPassword();
        $result['cart_qty'] = Mage::helper('checkout/cart')->getSummaryCount();
        $result['user_name'] = $name;        
        $information['data'] = array($result);
        return $information;
    }

    protected function getCustomerByEmail($email, $website_id) {//add them
        $collection = Mage::getModel('customer/customer')->getCollection()
                ->addFieldToFilter('email', $email);
        if (Mage::getStoreConfig('customer/account_share/scope')) {
            $collection->addFieldToFilter('website_id', $website_id);
        }
        return $collection->getFirstItem();
    }

    protected function createCustomer($data) {
        $customer = Mage::getModel('customer/customer')
                ->setFirstname($data['first_name'])
                ->setLastname($data['last_name'])
                ->setEmail($data['email']);

        $isSendPassToCustomer = true;
        $newPassword = $customer->generatePassword();
        $this->_pass = $newPassword;
        $customer->setPassword($newPassword);
        try {
            $customer->save();
        } catch (Exception $e) {
            
        }

        if ($isSendPassToCustomer)
            $customer->sendPasswordReminderEmail();
        return $customer;
    }

    // add them 
    protected function createCustomerMultiWebsite($data, $website_id, $store_id) {
        $customer = Mage::getModel('customer/customer');
        $customer->setFirstname($data['first_name'])
                ->setLastname($data['last_name'])
                ->setEmail($data['email'])
                ->setWebsiteId($website_id)
                ->setStoreId($store_id)
                ->save();
        
        //hainh
        $customer->setConfirmation(null);
        $customer->save();
        
        $isSendPassToCustomer = true;
        $newPassword = md5('simicart'.$data['email']);
        $this->_pass = $newPassword;
        $customer->setPassword($newPassword);
        try {
            $customer->save();
        } catch (Exception $e) {
            if (is_array($e->getMessage())) {
                return $e->getMessage();
            } else {
                return array($e->getMessage());
            }
        }

        if ($isSendPassToCustomer)
            $customer->sendPasswordReminderEmail();
        return $customer;
    }
    
    public function editProfile($name, $customer){
        $customer->setFirstname($name['first_name']);
        $customer->setLastname($name['last_name']);
        $customer->save();
    }

}