<?php
/**
 * Simi
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Simi.com license that is
 * available through the world-wide-web at this URL:
 * http://www.simi.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @copyright   Copyright (c) 2012 Simi (http://www.simi.com/)
 * @license     http://www.simi.com/license-agreement.html
 */

/** @var $installer Simi_Simirewardpoints_Model_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create simirewardpoints table and fields
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('simirewardpoints/transaction')};
DROP TABLE IF EXISTS {$this->getTable('simirewardpoints/rate')};
DROP TABLE IF EXISTS {$this->getTable('simirewardpoints/customer')};

CREATE TABLE {$this->getTable('simirewardpoints/customer')} (
  `reward_id` int(10) unsigned NOT NULL auto_increment,
  `customer_id` int(10) unsigned NOT NULL,
  `point_balance` int(11) NOT NULL default '0',
  `holding_balance` int(11) NOT NULL default '0',
  `spent_balance` int(11) NOT NULL default '0',
  `is_notification` smallint(5) NOT NULL,
  `expire_notification` smallint(5) NOT NULL,
  PRIMARY KEY (`reward_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('simirewardpoints/rate')} (
  `rate_id` int(10) unsigned NOT NULL auto_increment,
  `website_ids` text NULL,
  `customer_group_ids` text NULL,
  `direction` smallint(5) NOT NULL,
  `points` int(11) NOT NULL default '0',
  `money` decimal(12,4) NOT NULL default '0',
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`rate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('simirewardpoints/transaction')} (
  `transaction_id` int(10) unsigned NOT NULL auto_increment,
  `reward_id` int(10) unsigned NULL,
  `customer_id` int(10) unsigned NULL,
  `customer_email` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `action` varchar(63) NOT NULL,
  `action_type` smallint(5) NOT NULL default '0',
  `store_id` smallint(5) NOT NULL,
  `point_amount` int(11) NOT NULL default '0',
  `point_used` int(11) NOT NULL default '0',
  `real_point` int(11) NOT NULL default '0',
  `status` smallint(5) NOT NULL,
  `created_time` datetime NULL,
  `updated_time` datetime NULL,
  `expiration_date` datetime NULL,
  `expire_email` smallint(5) NOT NULL default '0',
  `order_id` int(10) unsigned NULL,
  `order_increment_id` varchar(63) NULL,
  `order_base_amount` decimal(12,4) NULL,
  `order_amount` decimal(12,4) NULL,
  `base_discount` decimal(12,4) NULL,
  `discount` decimal(12,4) NULL,
  `extra_content` text NULL,
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'simirewardpoints_earn', 'int(11) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'simirewardpoints_spent', 'int(11) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'simirewardpoints_base_discount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'simirewardpoints_discount', 'decimal(12,4) NOT NULL default 0');

$installer->getConnection()->addColumn($this->getTable('sales/order_item'), 'simirewardpoints_earn', 'int(11) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/order_item'), 'simirewardpoints_spent', 'int(11) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/order_item'), 'simirewardpoints_base_discount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/order_item'), 'simirewardpoints_discount', 'decimal(12,4) NOT NULL default 0');

$installer->getConnection()->addColumn($this->getTable('sales/invoice'), 'simirewardpoints_base_discount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/invoice'), 'simirewardpoints_discount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/creditmemo'), 'simirewardpoints_base_discount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn($this->getTable('sales/creditmemo'), 'simirewardpoints_discount', 'decimal(12,4) NOT NULL default 0');


/**
 * update database from customer reward
 */
$installer->updateSimirewardpointsConfig()
    ->updateSimirewardpointsRate()
    ->updateSimirewardpointsCustomer()
    ->updateSimirewardpointsTransaction()
    ->updateSimirewardpointsOrders()
    ->updateSimirewardpointsOrderTable();

$installer->endSetup();
