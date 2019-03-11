<?php
/**
 * Copyright © 2016 Simi. All rights reserved.
 */

namespace Simi\Simibarclays\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
	
        $installer = $setup;
        $installer->startSetup();

        $installer->run("DROP TABLE IF EXISTS {$installer->getTable('simibarclays_transaction')};");
        $installer->run("
        CREATE TABLE {$installer->getTable('simibarclays_transaction')} (
              `entity_id` int(11) unsigned NOT NULL auto_increment,
              `order_id` varchar(255) NULL,
              `status` varchar(255) DEFAULT 'pending',
              `amount` varchar(255) NULL,    
              `currency_code` varchar(255) NULL,  
              `token` varchar(255) NULL,
              `additional_data` text NULL DEFAULT '',
              PRIMARY KEY (`entity_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $installer->endSetup();

    }
}
