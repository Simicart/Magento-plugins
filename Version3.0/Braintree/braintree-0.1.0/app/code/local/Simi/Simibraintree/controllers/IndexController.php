<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.simcart.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Magestore
 * @package 	Magestore_Paypalmobile
 * @copyright 	Copyright (c) 2012 Magestore (http://www.simcart.com/)
 * @license 	http://www.simcart.com/license-agreement.html
 */

/**
 * Simibraintree Index Controller
 * 
 * @category 	Simi
 * @package 	Simi_Simibraintree
 * @author  	Simi Developer
 */
class Simi_Simibraintree_IndexController extends Simi_Connector_Controller_Action {

    public function update_paymentAction() {
        $data = $this->getData();
        $information = Mage::getModel('simibraintree/simibraintree')->updateBraintreePayment($data);
        $this->_printDataJson($information);
    }
	
	public function checkInstallAction(){
		echo "1";
		exit();
    }
   
	public function installDbAction(){
		 $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("		
			DROP TABLE IF EXISTS {$setup->getTable('simibraintree')};

			CREATE TABLE {$setup->getTable('simibraintree')} (
			  `braintree_id` int(11) unsigned NOT NULL auto_increment,
			  `transaction_id` varchar(255) NULL, 
			  `transaction_name` varchar(255) NULL,
			  `transaction_email` varchar(255) NULL,
			  `status` varchar(255) NULL,
			  `amount` varchar(255) NULL,    
			  `currency_code` varchar(255) NULL,  
			  `transaction_dis` varchar(255) NULL,
			  `order_id` int(11) NULL,  
			  PRIMARY KEY (`braintree_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
        $installer->endSetup();
        echo "success";
	}
}