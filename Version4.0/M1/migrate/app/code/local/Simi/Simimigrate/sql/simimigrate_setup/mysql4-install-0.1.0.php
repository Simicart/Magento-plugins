<?php

$installer = $this;
$installer->startSetup();

$installer->run("
    DROP TABLE IF EXISTS {$installer->getTable('simimigrate_app')};
    DROP TABLE IF EXISTS {$installer->getTable('simimigrate_store')};
    DROP TABLE IF EXISTS {$installer->getTable('simimigrate_storeview')};
    DROP TABLE IF EXISTS {$installer->getTable('simimigrate_category')};
    DROP TABLE IF EXISTS {$installer->getTable('simimigrate_product')};    
    DROP TABLE IF EXISTS {$installer->getTable('simimigrate_customer')};  
    DROP TABLE IF EXISTS {$installer->getTable('simimigrate_order')};

    CREATE TABLE {$installer->getTable('simimigrate_app')} (
        `app_id` int(11) unsigned NOT NULL auto_increment,
        `simicart_app_config_id` int(11) unsigned NOT NULL,
        `simicart_customer_id` varchar(255),
        `website_url` varchar(255) NULL default '',
        `user_email` varchar(255),
        `user_name` varchar(255),
        PRIMARY KEY (`app_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    CREATE TABLE {$installer->getTable('simimigrate_store')} (
        `entity_id` int(11) unsigned NOT NULL auto_increment,
        `app_id` varchar(255) NULL, 
        `group_id` varchar(255) NULL default '',        
        `website_id` varchar(255) NULL default '',
        `root_category_id` varchar(255) NULL,
        `default_store_id` varchar(255) NULL,
        `name` varchar(255) NULL,
        PRIMARY KEY (`entity_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;   
        
    CREATE TABLE {$installer->getTable('simimigrate_storeview')} (
        `entity_id` int(11) unsigned NOT NULL auto_increment,  
        `app_id` varchar(255) NULL,     
        `storeview_id` varchar(255) NULL default '',
        `group_id` varchar(255) NULL default '',        
        `website_id` varchar(255) NULL default '',
        `code` varchar(255) NULL default '',
        `name` varchar(255) NULL default '',
        `sort_order` varchar(255) NULL default '',
        `is_active` varchar(255) NULL default '',
        PRIMARY KEY (`entity_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;  
    
    CREATE TABLE {$installer->getTable('simimigrate_category')} (
        `entity_id` int(11) unsigned NOT NULL auto_increment,      
        `app_id` varchar(255) NULL, 
        `category_id` varchar(255) NULL default '',
        `parent_id` varchar(255) NULL default '',        
        `path` varchar(255) NULL default '',
        `position` varchar(255) NULL default '',
        `level` varchar(255) NULL default '',
        `children_count` varchar(255) NULL default '',
        `name` varchar(255) NULL default '',
        `url_path` varchar(255) NULL default '',
        PRIMARY KEY (`entity_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;  
    
    CREATE TABLE {$installer->getTable('simimigrate_product')} (
        `entity_id` int(11) unsigned NOT NULL auto_increment,     
        `app_id` varchar(255) NULL,  
        `product_id` varchar(255) NULL default '',     
        `sku` varchar(255) NULL default '', 
        `has_options` varchar(255) NULL default '',        
        `required_options` varchar(255) NULL default '',
        `name` varchar(255) NULL default '',
        `is_salable` varchar(255) NULL default '',
        PRIMARY KEY (`entity_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;  
        
    CREATE TABLE {$installer->getTable('simimigrate_customer')} (
        `entity_id` int(11) unsigned NOT NULL auto_increment,
        `app_id` varchar(255) NULL,       
        `customer_id` varchar(255) NULL default '',     
        `website_id` varchar(255) NULL default '',  
        `group_id` varchar(255) NULL default '',  
        `email` varchar(255) NULL default '',
        PRIMARY KEY (`entity_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
    CREATE TABLE {$installer->getTable('simimigrate_order')} (
        `entity_id` int(11) unsigned NOT NULL auto_increment,
        `app_id` varchar(255) NULL,       
        `order_id` varchar(255) NULL default '',     
        `status` varchar(255) NULL default '', 
        `state` varchar(255) NULL default '',        
        `customer_email` varchar(255) NULL default '',
        PRIMARY KEY (`entity_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;  

");

$installer->endSetup();
