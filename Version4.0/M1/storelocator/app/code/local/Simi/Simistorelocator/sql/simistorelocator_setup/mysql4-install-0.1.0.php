<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create simistorelocator table
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('simistorelocator')};
DROP TABLE IF EXISTS {$this->getTable('simistorelocator_image')};
DROP TABLE IF EXISTS {$this->getTable('simistorelocator_value')};


CREATE TABLE {$this->getTable('simistorelocator')} (
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

CREATE TABLE {$this->getTable('simistorelocator_image')} (
`image_id` int(11) unsigned NOT NULL auto_increment,
`image_delete` int(11),
`options` int(11),
`name` varchar(255),
`statuses` int(11),
`simistorelocator_id` int(11) unsigned NOT NULL,
INDEX(`simistorelocator_id`),
FOREIGN KEY (`simistorelocator_id`) REFERENCES {$this->getTable('simistorelocator')} (`simistorelocator_id`) ON DELETE CASCADE ON UPDATE CASCADE,
PRIMARY KEY (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;        

CREATE TABLE {$this->getTable('simistorelocator_value')} (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `simistorelocator_id` int(11) unsigned NOT NULL,
  `store_id` smallint(5) unsigned  NOT NULL,
  `attribute_code` varchar(63) NOT NULL default '',
  `value` text NOT NULL,
  UNIQUE(`simistorelocator_id`,`store_id`,`attribute_code`),
  INDEX (`simistorelocator_id`),
  INDEX (`store_id`),
  FOREIGN KEY (`simistorelocator_id`) REFERENCES {$this->getTable('simistorelocator')} (`simistorelocator_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core/store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (`value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;   

DROP TABLE IF EXISTS {$this->getTable('simistorelocator_tag')};
    CREATE TABLE {$this->getTable('simistorelocator_tag')} (
        `tag_id` int(10) unsigned NOT NULL auto_increment,
        `simistorelocator_id` int(11) unsigned NOT NULL,
        `value` varchar(2555),        
        INDEX (`simistorelocator_id`),
        FOREIGN KEY (`simistorelocator_id`) REFERENCES {$this->getTable('simistorelocator')} (`simistorelocator_id`) ON DELETE CASCADE ON UPDATE CASCADE,
       PRIMARY KEY (`tag_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;          
");

$installer->endSetup();

