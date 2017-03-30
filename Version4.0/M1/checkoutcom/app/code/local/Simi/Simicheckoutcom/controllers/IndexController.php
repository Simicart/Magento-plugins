<?php

/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simicheckoutcom
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Simicheckoutcom Adminhtml Controller
 * 
 * @category    
 * @package     Simicheckoutcom
 * @author      Developer
 */
class Simi_Simicheckoutcom_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * index action
     */
    public function indexAction() {
        Zend_Debug::dump(Mage::getStoreConfig("payment/simicheckoutcom/url_back"));
    }

    /**
     * index action
     */
    public function checkInstallAction() {
        echo "1";
        exit();
    }

    public function installDbAction() {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("		
			DROP TABLE IF EXISTS {$setup->getTable('simicheckoutcom')};

			CREATE TABLE {$setup->getTable('simicheckoutcom')} (
			  `simicheckoutcom_id` int(11) unsigned NOT NULL auto_increment,
			  `transaction_id` varchar(255) NOT NULL default '',
			  `transaction_name` varchar(255) NOT NULL default '',
			  `transaction_email` text NOT NULL default '',
			  `status` smallint(6) NOT NULL default '0',  
			  `currency_code` datetime NULL,
			  `order_id` int(11) NULL,  
			  PRIMARY KEY (`simicheckoutcom_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
		");
        $installer->endSetup();
        echo "success";
    }

    public function startCheckoutcomAction() {
        $orderId = $this->getRequest()->getParam('order_id');
        echo Mage::helper("simicheckoutcom")->getPaymentToken($orderId);
    }

    public function paymentrestv22Action() {
        $paymentToken = $this->getRequest()->getParam('payment_token');
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        if (Mage::getStoreConfig("payment/simicheckoutcom/is_sandbox")) {
            echo "<script src=\"https://sandbox.checkout.com/js/checkout.js\"></script>";
        } else {
            echo "<script src=\"https://cdn.checkout.com/js/checkout.js\"></script>";
        }
        echo "
        <form method=\"POST\" class=\"payment-form\">
            <script>
                Checkout.render({
                    debugMode: false,
                    publicKey: '" . Mage::getStoreConfig("payment/simicheckoutcom/public_key") . "',
                    paymentToken: '" . $paymentToken . "',
                    customerEmail: '" . $order->getData('customer_email') . "',
                    customerName: '" . $order->getData('customer_firstname') . ' ' . $order->getData('customer_middlename') . ' ' . $order->getData('customer_lastname') . "',
                    value: " . floatval($order->getData('grand_total')) * 100 . ",
                    currency: '" . $order->getData('order_currency_code') . "',
                    widgetContainerSelector: '.payment-form',
                    widgetColor: '#333',
                    themeColor: '#3075dd',
                    buttonColor:'#51c470',
                    logoUrl: \"http://www.merchant.com/images/logo.png\",
                });
            </script>
        </form>
        ";
        exit();
    }

}
