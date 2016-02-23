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
class Simi_Simicheckoutcom_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
	 * index action
	 */
	public function indexAction(){
		Zend_Debug::dump(Mage::getStoreConfig("payment/simicheckoutcom/url_back"));
	}
	
	/**
     * index action
     */
    public function checkInstallAction() {
        echo "1";
        exit();
    }
	
	public function installDbAction(){
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
}