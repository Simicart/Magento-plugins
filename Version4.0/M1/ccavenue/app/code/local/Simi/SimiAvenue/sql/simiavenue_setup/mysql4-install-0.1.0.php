<?php
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create simiavenue table
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('simiavenue')};

CREATE TABLE {$this->getTable('simiavenue')} (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();

