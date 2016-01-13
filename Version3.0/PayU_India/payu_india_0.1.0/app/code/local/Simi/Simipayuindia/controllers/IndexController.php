<?php

class Simi_Simipayuindia_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		$this->loadLayout();
		$this->renderLayout();
	}

	public function checkInstallAction(){
		echo "1";
		exit();
	}

	public function checkOrderAction(){
		$pam = Mage::helper("simipayuindia")->getUrlCheckout();
		Zend_debug::dump($pam);die();
		// $url = Mage::helper("simipayuindia")->getPayuCheckoutSharedUrl();
		// Zend_debug::dump($url);

		$this->getResponse()->setBody(
                $this->getLayout()
                    ->createBlock('simipayuindia/redirect')
                    ->toHtml()
            );        
		
		
	}

	public function installDbAction(){
		$setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("     
               DROP TABLE IF EXISTS {$setup->getTable('simipayuindia')};
				CREATE TABLE {$setup->getTable('simipayuindia')} (
				  `simipayuindia_id` int(10) unsigned NOT NULL auto_increment,
				  `debug_at` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
				  `request_body` text,
				  `response_body` text,
				  PRIMARY KEY  (`simipayuindia_id`),
				  KEY `debug_at` (`debug_at`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        ");
          $installer->endSetup();
          echo "success";
	}
}