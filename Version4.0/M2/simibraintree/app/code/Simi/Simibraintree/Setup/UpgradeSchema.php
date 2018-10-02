<?php

/**
 * Copyright © 2018 Simi. All rights reserved.
 */

namespace Simi\Simibraintree\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
    	$installer = $setup;
        $installer->startSetup();

        //handle all possible upgrade versions

        if(!$context->getVersion()) {
            //no previous version found, installation, InstallSchema was just executed
            //be careful, since everything below is true for installation !
        }

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            //code to upgrade to 1.0.1
            $installer->run("DROP TABLE IF EXISTS {$installer->getTable('simibraintree')};");
	        $installer->run("
			CREATE TABLE {$installer->getTable('simibraintree')} (
			      `braintree_id` int(11) unsigned NOT NULL auto_increment,
			      `transaction_id` varchar(255) NULL, 
			      `transaction_name` varchar(255) NULL,
			      `transaction_email` varchar(255) NULL,
			      `status` varchar(255) NULL,
			      `amount` varchar(255) NULL,    
			      `currency_code` varchar(255) NULL,  
			      `transaction_dis` varchar(255) NULL,
			      `order_id` int(11) NULL, 
			      `additional_data` text NULL DEFAULT '',
			      PRIMARY KEY (`braintree_id`)
			    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			");
        }

        $installer->endSetup();
    }
}