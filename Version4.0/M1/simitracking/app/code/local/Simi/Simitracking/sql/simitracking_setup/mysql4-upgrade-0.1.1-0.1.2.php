<?php

$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('simitracking/role'), 'is_owner_role', 'int (11) default 1');

$installer->endSetup();
