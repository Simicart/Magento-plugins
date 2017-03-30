<?php

class Simi_Simitracking_Helper_Data extends Mage_Core_Helper_Abstract {

    const SALE_TRACKING = '1';
    const TOTAL_DETAIL = '2';
    const SALE_DETAIL = '3';
    const PRODUCT_LIST = '4';
    const PRODUCT_DETAIL = '5';
    const PRODUCT_EDIT = '6';
    const CUSTOMER_LIST = '7';
    const CUSTOMER_DETAIL = '8';
    const CUSTOMER_EDIT = '9';
    const CUSTOMER_ADDRESS_LIST = '10';
    const CUSTOMER_ADDRESS_EDIT = '11';
    const CUSTOMER_ADDRESS_REMOVE = '12';
    const ORDER_LIST = '13';
    const ORDER_DETAIL = '14';
    const INVOICE_ORDER = '15';
    const SHIP_ORDER = '16';
    const CANCEL_ORDER = '17';
    const HOLD_ORDER = '18';
    const UNHOLD_ORDER = '19';
    const ABANDONED_CARTS_LIST = '20';
    const ABANDONED_CARTS_DETAILS = '21';

    public function getPermissionSections() {
        return array(
            "1" => $this->__("Store Statistics"),
            "2" => $this->__("Catalog Management"),
            "3" => $this->__("Customer Management"),
            "4" => $this->__("Order Management"),
            "5" => $this->__("Abandoned Carts"),
        );
    }

    public function getPermissions() {
        return array(
            self::SALE_TRACKING => array(
                "title" => $this->__("Sales Tracking"),
                "section" => "1"),
            self::TOTAL_DETAIL => array(
                "title" => $this->__("Totals Details"),
                "section" => "1"),
            self::SALE_DETAIL => array(
                "title" => $this->__("Lifetime Sales"),
                "section" => "1"),
            self::PRODUCT_LIST => array(
                "title" => $this->__("Product List"),
                "section" => "2"),
            self::PRODUCT_DETAIL => array(
                "title" => $this->__("Product Details"),
                "section" => "2"),
            self::PRODUCT_EDIT => array(
              "title" => $this->__("Product Edit"),
              "section" => "2"),
            self::CUSTOMER_LIST => array(
                "title" => $this->__("Customer List"),
                "section" => "3"),
            self::CUSTOMER_DETAIL => array(
                "title" => $this->__("Customer Details"),
                "section" => "3"),
            self::CUSTOMER_EDIT => array(
                "title" => $this->__("Customer Edit"),
                "section" => "3"),
            self::CUSTOMER_ADDRESS_LIST => array(
                "title" => $this->__("Customer Address List"),
                "section" => "3"),
            /*
            self::CUSTOMER_ADDRESS_EDIT => array(
                "title" => $this->__("Customer Address Edit"),
                "section" => "3"),
            self::CUSTOMER_ADDRESS_REMOVE => array(
                "title" => $this->__("Customer Address Remove"),
                "section" => "3"),
             */
            self::ORDER_LIST => array(
                "title" => $this->__("Order List"),
                "section" => "4"),
            self::ORDER_DETAIL => array(
                "title" => $this->__("Order Detail"),
                "section" => "4"),
            self::INVOICE_ORDER => array(
                "title" => $this->__("Invoice Order"),
                "section" => "4"),
            self::SHIP_ORDER => array(
                "title" => $this->__("Ship Order"),
                "section" => "4"),
            self::CANCEL_ORDER => array(
                "title" => $this->__("Cancel Order"),
                "section" => "4"),
            self::HOLD_ORDER => array(
                "title" => $this->__("Hold Order"),
                "section" => "4"),
            self::UNHOLD_ORDER => array(
                "title" => $this->__("Unhold Order"),
                "section" => "4"),
            self::ABANDONED_CARTS_LIST => array(
                "title" => $this->__("Abandoned Carts List"),
                "section" => "5"),
            self::ABANDONED_CARTS_DETAILS => array(
                "title" => $this->__("Abandoned Carts Details"),
                "section" => "5"),
        );
    }

    /*
     * $permissionId follow the list at const list
     */

    public function isAllowed($permissionId) {
        if (!Mage::getSingleton('customer/session')->isLoggedIn())
            throw new Exception(Mage::helper('simitracking')->__('Please Login'), 4);
        $user = Mage::getModel('simitracking/user')->getCollection()
                        ->addFieldToFilter('user_email', Mage::getSingleton('customer/session')->getCustomer()->getEmail())->getFirstItem();
        $permissionList = Mage::getModel('simitracking/permission')->getCollection()
                ->addFieldToFilter('role_id', $user->getRoleId())
                ->addFieldToFilter('permission_id', $permissionId);
        if (!$permissionList->count())
            throw new Exception($this->__('Permission Denied'), 4);
    }
    
    /* 
     * Continue session
     */

    public function continueSessionWithSessionId($sessionId) {
        $existed_device = Mage::getModel('simitracking/device')->getCollection()->addFieldToFilter('session_id', $sessionId)->getFirstItem();
        if ($existed_device->getId()) {
            /*
            if (time() > $existed_device->getData('session_deadline')) {
                throw new Exception(Mage::helper('simitracking')->__('Your session has expired. Please log in again'), 4);
            }
             */
            $customer = Mage::helper('simiconnector/customer')->getCustomerByEmail($existed_device->getData('user_email'));
            if ($customer->getId()) {
                Mage::helper('simiconnector/customer')->loginByCustomer($customer);
                return true;
            }
        }
    }
}
