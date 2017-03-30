<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Twout
 * @copyright 	Copyright (c) 2012 
 * @license 	
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create twout table
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('twout')};

CREATE TABLE {$this->getTable('twout')} (
  `twout_id` int(11) unsigned NOT NULL auto_increment,
  `transaction_id` varchar(255) NOT NULL default '',
  `transaction_name` varchar(255) NOT NULL default '',
  `transaction_email` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',  
  `currency_code` datetime NULL,
  `order_id` int(11) NULL,  
  PRIMARY KEY (`twout_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();

