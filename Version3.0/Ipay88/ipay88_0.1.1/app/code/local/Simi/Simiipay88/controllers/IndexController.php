<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Simiipay88
 * @copyright 	Copyright (c) 2012 
 * @license 	
 */

 /**
 * Simiipay88 Controller
 * 
 * @category 	
 * @package 	Simiipay88
 * @author  	Developer
 */
class Simi_Simiipay88_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
	 * index action
	 */
	public function checkInstallAction(){
		echo "1";
		exit();
	}
	
	public function installDbAction(){
		 $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("		
				DROP TABLE IF EXISTS {$setup->getTable('simiipay88')};

			CREATE TABLE {$setup->getTable('simiipay88')} (
			  `simiipay88_id` int(11) unsigned NOT NULL auto_increment,
			  `transaction_id` varchar(255) NOT NULL default '',
			  `order_id` varchar(255) NOT NULL default '',
			  `auth_code` varchar(255) NULL default '', 
			  `ref_no` varchar(255) NULL default '',
			  `status` int(11) NULL default '0',
			  PRIMARY KEY (`simiipay88_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
        $installer->endSetup();
        echo "success";
	}
}