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

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create simiipay88 table
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('simiipay88')};

CREATE TABLE {$this->getTable('simiipay88')} (
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

