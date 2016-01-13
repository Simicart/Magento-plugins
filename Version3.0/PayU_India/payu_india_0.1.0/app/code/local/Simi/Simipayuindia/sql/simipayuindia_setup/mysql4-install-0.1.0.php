<?php

$installer = $this;
$installer->startSetup();
//log debug
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('simipayuindia')};
CREATE TABLE {$this->getTable('simipayuindia')} (
  `simipayuindia_id` int(10) unsigned NOT NULL auto_increment,
  `debug_at` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `request_body` text,
  `response_body` text,
  PRIMARY KEY  (`simipayuindia_id`),
  KEY `debug_at` (`debug_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 