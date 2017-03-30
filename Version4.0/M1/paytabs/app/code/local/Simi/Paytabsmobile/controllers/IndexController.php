<?php

/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Paytabsmobile
 * @copyright   Copyright (c) 2012
 * @license     
 */

/**
 * Paytabsmobile Controller
 * 
 * @category    
 * @package     Paytabsmobile
 * @author      Developer
 */
class Simi_Paytabsmobile_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * index action
     */
    public function testAction() {
        $train = Mage::getModel('paytabsmobile/paytabsmobile')->getCollection();
        Zend_debug::dump($train->getData());
        die();
    }

    public function preDispatch() {
        parent::preDispatch();
        $value = $this->getRequest()->getParam('data');
        $this->praseJsonToData($value);
    }

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function checkInstallAction() {
        echo "1";
        exit();
    }

    public function installDbAction() {
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("		
			DROP TABLE IF EXISTS {$setup->getTable('paytabsmobile')};

			CREATE TABLE {$setup->getTable('paytabsmobile')} (
			  `paytabsmobile_id` int(11) unsigned NOT NULL auto_increment,
			  `transaction_id` varchar(255) NULL, 
			  `transaction_name` varchar(255) NULL,
			  `transaction_email` varchar(255) NULL,
			  `status` varchar(255) NULL,
			  `amount` varchar(255) NULL,    
			  `currency_code` varchar(255) NULL,  
			  `transaction_dis` varchar(255) NULL,
			  `order_id` int(11) NULL,  
			  PRIMARY KEY (`paytabsmobile_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
        $installer->endSetup();
        echo "success";
    }

}
