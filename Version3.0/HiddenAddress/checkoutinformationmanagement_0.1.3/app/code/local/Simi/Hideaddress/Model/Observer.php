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
 * @package     Magestore_Hideaddress
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Hideaddress Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Hideaddress
 * @author      Magestore Developer
 */
class Simi_Hideaddress_Model_Observer extends Simi_Connector_Model_Observer{

    /**
     * process controller_action_predispatch event
     *
     * @return Simi_Hideaddress_Model_Observer
     */
    
     public function paymentMethodIsActive($observer) {
       
        $result = $observer['result'];
        $method = $observer['method_instance'];
        //$store = $quote ? $quote->getStoreId() : null;            
        if ($method->getCode() == 'transfer_mobile') {
            if (Mage::app()->getRequest()->getControllerModule() != 'Simi_Connector' && Mage::app()->getRequest()->getControllerModule() != 'Simi_Hideaddress') {
                $result->isAvailable = false;
            }
        }
    }
    
    public function controllerActionPredispatch($observer) {
        $action = $observer->getEvent()->getControllerAction();
        return $this;
    }

    public function addCondition($observer) {        
        $object = $observer->getObject();
        $data = $object->getCacheData();
          if (Mage::getStoreConfig('hideaddress/general/enable') == 0) { 
            $agreements = Mage::helper('connector/checkout')->getAgreements();
            $conditions = array();
            foreach ($agreements as $agreement) {
                if ($agreement->getIsHtml()) {
                    $conditions[] = array(
                        'id' => $agreement->getId(),
                        'name' => $agreement->getName(),
                        'title' => $agreement->getCheckboxText(),
                        'content' => $agreement->getContent(),
                    );
                } else {
                    $conditions[] = array(
                        'id' => $agreement->getId(),
                        'name' => $agreement->getName(),
                        'title' => $agreement->getCheckboxText(),
                        'content' => nl2br(Mage::helper('connector')->escapeHtml($agreement->getContent())),
                    );
                }
            }
            $data['condition'] = $conditions;
            $object->setCacheData($data, "simi_connector");
          } else if (Mage::getStoreConfig('hideaddress/general/enable') == 1) { 
            $show = Mage::getStoreConfig('hideaddress/terms_conditions/enable_terms');
            $conditions = array();
            if ($show) {
                $term_title = Mage::getStoreConfig('hideaddress/terms_conditions/term_title');
                $term_html = Mage::getStoreConfig('hideaddress/terms_conditions/term_html');
                $condition=array();
                $condition['id']=-1;
                $condition['name']="Terms and conditions";
                $condition['title'] = $term_title;
                $condition['content'] = $term_html;
                $conditions[]=$condition;
            }    
            $data['condition'] = $conditions;
            $object->setCacheData($data, "simi_connector");
        }
           
        return;
    }

    public function changeData($observer){
        $object = $observer->getObject();
        $base_data = $object->getData();
        $data = $base_data;
        if (Mage::getStoreConfig('hideaddress/general/enable') == 1) {
            // Billing Address
            if ($data->billingAddress->name == null) {
                $data->billingAddress->name = "name";
            }
            if ($data->billingAddress->prefix == null) {
                $data->billingAddress->prefix = "";
            }
             if ($data->billingAddress->suffix == null) {
                $data->billingAddress->suffix = "";
            }
             if ($data->billingAddress->email == null) {
                $data->billingAddress->email = "";
            }
            if ($data->billingAddress->street == null) {
                $data->billingAddress->street = "";
            }
            if ($data->billingAddress->phone == null) {
                $data->billingAddress->phone = "N/A";
            }
            if ($data->billingAddress->city == null) {
                $data->billingAddress->city = "N/A";
            }
            if ($data->billingAddress->country_code == null) {
                $data->billingAddress->country_code = "US";
            }
            if ($data->billingAddress->zip == null) {
                $data->billingAddress->zip = "N/A";
            }
            if ($data->billingAddress->state_name == null) {
                $data->billingAddress->state_name = "";
            }
            if ($data->billingAddress->state_id == null) {
                $data->billingAddress->state_id = "";
            }
            if ($data->billingAddress->company == null) {
                $data->billingAddress->company = "";
            }
            if ($data->billingAddress->fax == null) {
                $data->billingAddress->fax = "";
            }

            // Shipping Address
            if ($data->shippingAddress->name == null) {
                $data->shippingAddress->name = "name";
            }
            if ($data->shippingAddress->prefix == null) {
                $data->shippingAddress->prefix = "";
            }
             if ($data->shippingAddress->suffix == null) {
                $data->shippingAddress->suffix = "";
            }
             if ($data->shippingAddress->email == null) {
                $data->shippingAddress->email = "";
            }
            if ($data->shippingAddress->street == null) {
                $data->shippingAddress->street = "";
            }
            if ($data->shippingAddress->phone == null) {
                $data->shippingAddress->phone = "N/A";
            }
            if ($data->shippingAddress->city == null) {
                $data->shippingAddress->city = "N/A";
            }
            if ($data->shippingAddress->country_code == null) {
                $data->shippingAddress->country_code = "US";
            }
            if ($data->shippingAddress->zip == null) {
                $data->shippingAddress->zip = "N/A";
            }
            if ($data->shippingAddress->state_name == null) {
                $data->shippingAddress->state_name = "";
            }
            if ($data->shippingAddress->state_id == null) {
                $data->shippingAddress->state_id = "";
            }
        }
                
        $observer->setData($data);
    }

    public function changeDataAddress($observer){
        $object = $observer->getObject();
        $base_data = $object->getData();
        $data = $base_data;
        
        if (Mage::getStoreConfig('hideaddress/general/enable') == 1) {
            $data->address_id =  isset($data->address_id) == true ? $data->address_id : 0;
            $data->name = isset($data->name) == true ? $data->name : 'N/A';
            $data->street = isset($data->street) == true ? $data->street : 'N/A';// array($data->street, 'N/A');
            $data->city = isset($data->city) == true ? $data->city : 'N/A';
            $data->company = isset($data->company) == true ? $data->company : '';
            $data->state_code = isset($data->state_code) == true ? $data->state_code : 'N/A';
            $data->state_id = isset($data->state_id) == true ? $data->state_id : 0;
            $data->state_name = isset($data->state_name) == true ? $data->state_name : '';
            $data->zip = isset($data->zip) == true ? $data->zip : 'N/A';
            $data->country_code = isset($data->country_code) == true ? $data->country_code : 'N/A';
            $data->country_name = isset($data->country_name) == true ? $data->country_name : 'N/A';
            $data->phone = isset($data->phone) == true ? $data->phone : 'N/A';
            $data->email = isset($data->email) == true ? $data->email : '';
            $data->suffix = isset($data->suffix) == true ? $data->suffix : '';
            $data->prefix = isset($data->prefix) == true ? $data->prefix : '';
            $data->dob = isset($data->dob) == true ? $data->dob : '';
            $data->taxvat = isset($data->taxvat) == true ? $data->taxvax : '';
            $data->gender = isset($data->gender) == true ? $data->gender : Mage::getResourceSingleton('customer/customer')->getAttribute('gender')->getSource()->getOptionId('Male');
            $data->month = isset($data->month) == true ? $data->month : '';
            $data->day = isset($data->day) == true ? $data->day : '';
            $data->year = isset($data->year) == true ? $data->year : '';
            $data->fax = isset($data->fax) == true ? $data->fax : '';
        }                
        $observer->setData($data);
    }
}