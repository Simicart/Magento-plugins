<?php

$installer = $this;
$installer->startSetup();
$installer->run("
    DROP TABLE IF EXISTS {$installer->getTable('simitracking_user')};
    DROP TABLE IF EXISTS {$installer->getTable('simitracking_roles')};
    DROP TABLE IF EXISTS {$installer->getTable('simitracking_permissions')};
    DROP TABLE IF EXISTS {$installer->getTable('simitracking_devices')};
    DROP TABLE IF EXISTS {$installer->getTable('simitracking_notice_history')};
        
    CREATE TABLE {$installer->getTable('simitracking_user')} (
        `entity_id` int(11) unsigned NOT NULL auto_increment,
        `status` smallint(5) unsigned,
        `user_profile_image` varchar(255) default '',
        `user_title` varchar(255) default '',	
        `user_email` varchar(255) default '',
        `is_receive_notification` int (11) default 1,
        `notification_count` int (11) default 0,
        `role_id` int (11) NOT NULL,
      PRIMARY KEY (`entity_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE {$installer->getTable('simitracking_roles')} (
        `entity_id` int(11) unsigned NOT NULL auto_increment,
        `role_title` varchar(255) default '',
      PRIMARY KEY (`entity_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    CREATE TABLE {$installer->getTable('simitracking_permissions')} (
        `entity_id` int(11) unsigned NOT NULL auto_increment,
        `role_id` varchar(255) default '',
        `permission_id` varchar(255) default '',
      PRIMARY KEY (`entity_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    CREATE TABLE {$installer->getTable('simitracking_devices')} (
        `entity_id` int(11) unsigned NOT NULL auto_increment,
        `user_email` varchar(255) default '',
        `device_name` varchar(255) default '',
        `plaform_id` int (11),
        `is_key_token` int (11) default 0,
        `device_token` varchar(255) default '',
        `device_longitude` varchar(255) default '',
        `device_latitude` varchar(255) default '',
        `device_manufacture_number` varchar(255) default '',
        `device_user_agent` varchar(255) default '',
        `device_ip` varchar(255) default '',
        `created_time` datetime NOT NULL default '0000-00-00 00:00:00',
        `session_id` varchar(255) default '',
        `session_deadline` int (11),
      PRIMARY KEY (`entity_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE {$installer->getTable('simitracking_notice_history')} (
        `history_id` int(11) unsigned NOT NULL auto_increment,
        `notice_title` varchar(255) NULL default '', 
        `notice_content` text NULL default '',    
        `notice_sanbox` tinyint(1) NULL default '0',
        `storeview_id` int (11),
        `device_id` int (11),
        `type` smallint(5) unsigned,
        `show_popup` smallint(5) unsigned,
        `created_time` datetime NOT NULL default '0000-00-00 00:00:00',
        `notice_type` smallint(5) unsigned,
        `status` smallint(5) unsigned,
        `devices_pushed` text NULL default '',
    PRIMARY KEY (`history_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
");
$installer->endSetup();
