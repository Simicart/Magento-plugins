<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Simideeplink
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create simideeplink table
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('simideeplink')};

CREATE TABLE {$this->getTable('simideeplink')} (
  `simideeplink_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `product_id` varchar(255) NOT NULL default '',
  `category_id` varchar(255) NOT NULL default '',
  `cms_id` varchar(255) NOT NULL default '',
  `type` smallint(6) NOT NULL default '0',
  
  `social_title` varchar(255) NOT NULL default '',
  `social_description` varchar(255) NOT NULL default '',
  `social_image` varchar(255) NOT NULL default '',
  `utm_source` varchar(255) NOT NULL default '',
  `utm_medium` varchar(255) NOT NULL default '',
  `utm_campaign` varchar(255) NOT NULL default '',
  `utm_term` varchar(255) NOT NULL default '',
  `utm_content` varchar(255) NOT NULL default '',
  
  `gclid` varchar(255) NOT NULL default '',
  `at` varchar(255) NOT NULL default '',
  `ct` varchar(255) NOT NULL default '',
  `mt` varchar(255) NOT NULL default '',
  `pt` varchar(255) NOT NULL default '',
  
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`simideeplink_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();

