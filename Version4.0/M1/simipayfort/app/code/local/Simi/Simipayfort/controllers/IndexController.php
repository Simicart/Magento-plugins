<?php

/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simipayfort
 * @copyright   Copyright (c) 2017 
 * @license     
 */

/**
 * Simipayfort Adminhtml Controller
 * 
 * @category    
 * @package     Simipayfort
 * @author      Developer
 */
class Simi_Simipayfort_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * index action
     */
    public function indexAction() {
        Zend_Debug::dump(Mage::getStoreConfig("payment/simipayfort/url_back"));
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
			DROP TABLE IF EXISTS {$setup->getTable('simipayfort')};

			CREATE TABLE {$setup->getTable('simipayfort')} (
			  `simipayfort_id` int(11) unsigned NOT NULL auto_increment,
			  `transaction_id` varchar(255) NOT NULL default '',
			  `transaction_name` varchar(255) NOT NULL default '',
			  `transaction_email` text NOT NULL default '',
			  `status` smallint(6) NOT NULL default '0',  
			  `currency_code` datetime NULL,
			  `order_id` int(11) NULL,  
			  PRIMARY KEY (`simipayfort_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
		");
        $installer->endSetup();
        echo "success";
    }

    public function startPayfortAction() {
        $orderId = $this->getRequest()->getParam('order_id');
        echo Mage::helper("simipayfort")->getPaymentToken($orderId);
    }

    public function paymentrestv22Action() {
        $paymentToken = $this->getRequest()->getParam('payment_token');
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        if (Mage::getStoreConfig("payment/simipayfort/is_sandbox")) {
            echo "<script src=\"https://sandbox.checkout.com/js/checkout.js\"></script>";
        } else {
            echo "<script src=\"https://cdn.checkout.com/js/checkout.js\"></script>";
        }
        echo "
        <form method=\"POST\" class=\"payment-form\">
            <script>
                Checkout.render({
                    debugMode: false,
                    publicKey: '" . Mage::getStoreConfig("payment/simipayfort/public_key") . "',
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
