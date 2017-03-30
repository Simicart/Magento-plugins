<?php
/**
 *
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simiavenue
 * @copyright   Copyright (c) 2012 
 * @license    
 */

/**
 * Simiavenue Index Controller
 * 
 * @category    
 * @package     Simiavenue
 * @author      Developer
 */
class Simi_SimiAvenue_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * index action
     */
    public function checkInstallAction()
    {		
        echo "1";
		exit();
    }
	
	public function installDBAction(){
		$setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("		
			DROP TABLE IF EXISTS {$setup->getTable('simiavenue')};

CREATE TABLE {$setup->getTable('simiavenue')} (
  `simiavenue_id` int(11) unsigned NOT NULL auto_increment,
  `merchant_id` varchar(25) NOT NULL,
  `amount` float unsigned NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL,
  `merchant_param` varchar(255) NOT NULL,
  `checksum` varchar(255) NOT NULL,
  `authdesc` varchar(10) NOT NULL,
  `card_category` varchar(255) NOT NULL,
  `bank_name` varchar(255) NOT NULL,  
  PRIMARY KEY (`simiavenue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;	");
        $installer->endSetup();
        echo "success";
	}
}