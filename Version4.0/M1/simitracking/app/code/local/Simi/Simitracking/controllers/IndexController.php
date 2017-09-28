<?php

class Simi_Simitracking_IndexController extends Mage_Core_Controller_Front_Action
{


    public function installDBAction()
    {
        $setup = new Mage_Core_Model_Resource_Setup('core_setup');
        $installer = $setup;
        $installer->startSetup();
        
        $installer->run("
    DROP TABLE IF EXISTS {$installer->getTable('simitracking_user')};
        
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
        return $this->getResponse()->setBody('success');

    }
}
