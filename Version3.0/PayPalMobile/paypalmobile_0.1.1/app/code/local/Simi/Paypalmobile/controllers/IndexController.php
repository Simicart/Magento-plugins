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
 * @category 	Magestore
 * @package 	Magestore_Paypalmobile
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Paypalmobile Index Controller
 * 
 * @category 	Magestore
 * @package 	Magestore_Paypalmobile
 * @author  	Magestore Developer
 */
class Simi_Paypalmobile_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * index action
     */
	 
	protected $_data;

    public function preDispatch() {
        parent::preDispatch();
        $value = $this->getRequest()->getParam('data');
        $this->praseJsonToData($value);
    }
	public function convertToJson($data) {
        $this->setData($data);     
        $this->_data = $this->getData();
        return Mage::helper('core')->jsonEncode($this->_data);
    }
	
	public function praseJsonToData($json) {
        $data = json_decode($json);
        $this->setData($data);        
        $this->_data = $this->getData();
    }
	
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function update_paypal_paymentAction() {
        $data = $this->getData();
        $information = Mage::getModel('paypalmobile/paypalmobile')->updatePaypalPayment($data);
        $this->_printDataJson($information);
    }
	
	public function getData() {
        return $this->_data;
    }

    public function setData($data) {
        $this->_data = $data;
    }
	
	
	public function checkInstallAction(){
		echo "1";
		exit();
   }
   
   public function _printDataJson($data) {
        echo $this->convertToJson($data);
        header("Content-Type: application/json");
        exit();
    }
	
	public function installDbAction(){
		 $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("		
			DROP TABLE IF EXISTS {$setup->getTable('paypalmobile')};

			CREATE TABLE {$setup->getTable('paypalmobile')} (
			  `paypalmobile_id` int(11) unsigned NOT NULL auto_increment,
			  `transaction_id` varchar(255) NULL, 
			  `transaction_name` varchar(255) NULL,
			  `transaction_email` varchar(255) NULL,
			  `status` varchar(255) NULL,
			  `amount` varchar(255) NULL,    
			  `currency_code` varchar(255) NULL,  
			  `transaction_dis` varchar(255) NULL,
			  `order_id` int(11) NULL,  
			  PRIMARY KEY (`paypalmobile_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
        $installer->endSetup();
        echo "success";
	}
}