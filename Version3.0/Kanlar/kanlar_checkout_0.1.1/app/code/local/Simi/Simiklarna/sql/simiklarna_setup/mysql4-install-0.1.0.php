<?php

$installer = $this;
$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('simiklarna')};
CREATE TABLE {$this->getTable('simiklarna')} (
  `simiklarna_id` int(11) unsigned NOT NULL auto_increment,
  `reference` varchar(255) NULL default '',
  `reservation` varchar(255) NULL default '',
  `order_id` varchar(255) NULL default '',
  PRIMARY KEY (`simiklarna_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 