<?php

class Simi_Simiklarna_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction(){
		$this->loadLayout();
		$this->renderLayout();
	}

	public function checkInstallAction(){
		echo "1";
	}

    public function installDbAction(){
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("     
               -- DROP TABLE IF EXISTS {$setup->getTable('simiklarna')};
                CREATE TABLE {$setup->getTable('simiklarna')} (
                  `simiklarna_id` int(11) unsigned NOT NULL auto_increment,
                  `reference` varchar(255) NULL default '',
                  `reservation` varchar(255) NULL default '',
                  `order_id` varchar(255) NULL default '',
                  PRIMARY KEY (`simiklarna_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
          $installer->endSetup();
          echo "success";
    }
}