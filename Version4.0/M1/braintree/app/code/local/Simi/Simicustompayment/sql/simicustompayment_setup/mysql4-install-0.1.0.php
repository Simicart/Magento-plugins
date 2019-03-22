<?php

$installer = $this;

$installer->startSetup();
/*
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('simicustompayment')};

CREATE TABLE {$this->getTable('simicustompayment')} (
  `simicustompayment_id` int(11) unsigned NOT NULL auto_increment,
  `order_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`simicustompayment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
*/
$installer->endSetup();

