<?php

class Simi_Simistorelocator_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
	 * index action
	 */
	 // comment by Simi Team
	public function indexAction(){           
            //if(Mage::helper('simistorelocator')->getConfig('enable')){
		// $this->loadLayout();
                // $this->getLayout()->getBlock('head')->setTitle(Mage::helper('simistorelocator')->getConfig('page_title')); 
		// $this->renderLayout();
            //}
	}
        
        public function viewAction()
        {
            //renderlayout view detail store
            //if(Mage::helper('simistorelocator')->getConfig('enable')){
                // $this->loadLayout();
                // $this->getLayout()->getBlock('head')->setTitle(Mage::helper('simistorelocator')->getConfig('page_title')); 
                // $this->renderLayout();
            //}
        }
		
		// Add function check by SimiTeam
		public function checkInstallAction(){
			echo "1";
			exit();
		}
    public function installDBAction(){
		$setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("		
			DROP TABLE IF EXISTS {$setup->getTable('simistorelocator')};
DROP TABLE IF EXISTS {$setup->getTable('simistorelocator_image')};
DROP TABLE IF EXISTS {$setup->getTable('simistorelocator_value')};


CREATE TABLE {$setup->getTable('simistorelocator')} (
  `simistorelocator_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `address` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `country` varchar(255) NOT NULL default '',
  `zipcode` varchar(25) NULL default '',
  `state` varchar(255) NULL default '',
  `state_id` int(11) NULL ,
  `email` varchar(255) NULL default '',
  `phone` varchar(25) NULL default '',
  `fax` varchar(25) NULL default '',
  `description` text NOT NULL default '',
  `status` tinyint(1) NOT NULL default '0',
  `sort` int(10) NULL default 0,
  `link` varchar(255) NULL default '',
  `latitude` varchar(30) NULL default '',
  `longtitude` varchar(30) NULL default '',
  `zoom_level` int(11) NULL,
  `image_icon` varchar(255) NULL default '',
  PRIMARY KEY (`simistorelocator_id`)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1; 

CREATE TABLE {$setup->getTable('simistorelocator_image')} (
`image_id` int(11) unsigned NOT NULL auto_increment,
`image_delete` int(11),
`options` int(11),
`name` varchar(255),
`statuses` int(11),
`simistorelocator_id` int(11) unsigned NOT NULL,
INDEX(`simistorelocator_id`),
FOREIGN KEY (`simistorelocator_id`) REFERENCES {$setup->getTable('simistorelocator')} (`simistorelocator_id`) ON DELETE CASCADE ON UPDATE CASCADE,
PRIMARY KEY (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;        

CREATE TABLE {$setup->getTable('simistorelocator_value')} (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `simistorelocator_id` int(11) unsigned NOT NULL,
  `store_id` smallint(5) unsigned  NOT NULL,
  `attribute_code` varchar(63) NOT NULL default '',
  `value` text NOT NULL,
  UNIQUE(`simistorelocator_id`,`store_id`,`attribute_code`),
  INDEX (`simistorelocator_id`),
  INDEX (`store_id`),
  FOREIGN KEY (`simistorelocator_id`) REFERENCES {$setup->getTable('simistorelocator')} (`simistorelocator_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`store_id`) REFERENCES {$setup->getTable('core/store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (`value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;   

DROP TABLE IF EXISTS {$setup->getTable('simistorelocator_tag')};
    CREATE TABLE {$setup->getTable('simistorelocator_tag')} (
        `tag_id` int(10) unsigned NOT NULL auto_increment,
        `simistorelocator_id` int(11) unsigned NOT NULL,
        `value` varchar(2555),        
        INDEX (`simistorelocator_id`),
        FOREIGN KEY (`simistorelocator_id`) REFERENCES {$setup->getTable('simistorelocator')} (`simistorelocator_id`) ON DELETE CASCADE ON UPDATE CASCADE,
       PRIMARY KEY (`tag_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;   
	ALTER TABLE  {$setup->getTable('simistorelocator')}
    ADD COLUMN `monday_status` smallint(6) NOT NULL default '1'
    AFTER `longtitude`,
    ADD COLUMN `monday_open` varchar(5) NOT NULL default ''
    AFTER `monday_status`,
    ADD COLUMN `monday_close` varchar(5) NOT NULL default ''
    AFTER `monday_open`
    ;
    
    ALTER TABLE  {$setup->getTable('simistorelocator')}
    ADD COLUMN `tuesday_status` smallint(6) NOT NULL default '1'
    AFTER `monday_close`,
    ADD COLUMN `tuesday_open` varchar(5) NOT NULL default ''
    AFTER `tuesday_status`,
    ADD COLUMN `tuesday_close` varchar(5) NOT NULL default ''
    AFTER `tuesday_open`
    ;
   
    ALTER TABLE  {$setup->getTable('simistorelocator')}
    ADD COLUMN `wednesday_status` smallint(6) NOT NULL default '1'
    AFTER `tuesday_close`,
    ADD COLUMN `wednesday_open` varchar(5) NOT NULL default ''
    AFTER `wednesday_status`,
    ADD COLUMN `wednesday_close` varchar(5) NOT NULL default ''
    AFTER `wednesday_open`
    ;
    
     ALTER TABLE  {$setup->getTable('simistorelocator')}
    ADD COLUMN `thursday_status` smallint(6) NOT NULL default '1'
    AFTER `wednesday_close`,
    ADD COLUMN `thursday_open` varchar(5) NOT NULL default ''
    AFTER `thursday_status`,
    ADD COLUMN `thursday_close` varchar(5) NOT NULL default ''
    AFTER `thursday_open`
    ;
    
    ALTER TABLE  {$setup->getTable('simistorelocator')}
    ADD COLUMN `friday_status` smallint(6) NOT NULL default '1'
    AFTER `thursday_close`,
    ADD COLUMN `friday_open` varchar(5) NOT NULL default ''
    AFTER `friday_status`,
    ADD COLUMN `friday_close` varchar(5) NOT NULL default ''
    AFTER `friday_open`
    ;
    
    ALTER TABLE  {$setup->getTable('simistorelocator')}
    ADD COLUMN `saturday_status` smallint(6) NOT NULL default '1'
    AFTER `friday_close`,
    ADD COLUMN `saturday_open` varchar(5) NOT NULL default ''
    AFTER `saturday_status`,
    ADD COLUMN `saturday_close` varchar(5) NOT NULL default ''
    AFTER `saturday_open`
    ;
    
     ALTER TABLE  {$setup->getTable('simistorelocator')}
    ADD COLUMN `sunday_status` smallint(6) NOT NULL default '1'
    AFTER `saturday_close`,
    ADD COLUMN `sunday_open` varchar(5) NOT NULL default ''
    AFTER `sunday_status`,
    ADD COLUMN `sunday_close` varchar(5) NOT NULL default ''
    AFTER `sunday_open`
    ;
	
	DROP TABLE IF EXISTS {$setup->getTable('simistorelocator_specialday')};
    CREATE TABLE {$setup->getTable('simistorelocator_specialday')}  (
        `simistorelocator_specialday_id` int(11) NOT NULL auto_increment,
        `store_id` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
        `date` date NOT NULL,
        `specialday_date_to` date NOT NULL,      
        `specialday_time_open` varchar(5) NOT NULL,
        `specialday_time_close` varchar(5) NOT NULL,        
        `comment` varchar(255) default NULL,
      PRIMARY KEY  (`simistorelocator_specialday_id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
    
    DROP TABLE IF EXISTS {$setup->getTable('simistorelocator_holiday')};
    CREATE TABLE {$setup->getTable('simistorelocator_holiday')}  (
        `simistorelocator_holiday_id` int(11) NOT NULL auto_increment,
        `store_id` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
        `date` date NOT NULL,
        `holiday_date_to` date NOT NULL,     
        `comment` varchar(255) default NULL,
      PRIMARY KEY  (`simistorelocator_holiday_id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
		");
        $installer->endSetup();
        echo "success";
	}
}
