<?php

$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('product_label')}
	ADD `is_apply` smallint(6) NOT NULL default '2';
 ");

$installer->endSetup();
