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
 * @package 	Magestore_Simicheckoutcom
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create simicheckoutcom table
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('simicheckoutcom')};

CREATE TABLE {$this->getTable('simicheckoutcom')} (
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

