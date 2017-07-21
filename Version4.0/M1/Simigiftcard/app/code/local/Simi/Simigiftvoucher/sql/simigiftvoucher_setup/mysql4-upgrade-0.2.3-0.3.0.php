<?php

/**
 * Simi
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Simicart.com license that is
 * available through the world-wide-web at this URL:
 * http://www.Simicart.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Simi
 * @package     Simi_Simigiftvoucher
 * @module     Giftvoucher
 * @author      Simi Developer
 *
 * @copyright   Copyright (c) 2016 Simi (http://www.Simicart.com/)
 * @license     http://www.Simicart.com/license-agreement.html
 *
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$setup = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

/* add Gift Card product attribute */
$setup->removeAttribute('catalog_product', 'simishow_gift_amount_desc');
$setup->removeAttribute('catalog_product', 'simigift_amount_desc');
$setup->removeAttribute('catalog_product', 'simigiftcard_description');
$attr = array(
    'group' => 'Prices',
    'type' => 'int',
    'input' => 'boolean',
    'label' => 'Show description of gift card value',
    'backend' => '',
    'frontend' => '',
    'source' => '',
    'visible' => 1,
    'user_defined' => 1,
    'used_for_price_rules' => 1,
    'position' => 3,
    'unique' => 0,
    'default' => '',
    'sort_order' => 102,
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required' => 0,
    'apply_to' => 'simigiftvoucher',
    'is_configurable' => 1,
    'is_searchable' => 1,
    'is_visible_in_advanced_search' => 1,
    'is_comparable' => 0,
    'is_filterable' => 0,
    'is_filterable_in_search' => 1,
    'is_used_for_promo_rules' => 1,
    'is_html_allowed_on_front' => 0,
    'is_visible_on_front' => 0,
    'used_in_product_listing' => 1,
    'used_for_sort_by' => 0,
);
$setup->addAttribute('catalog_product', 'simishow_gift_amount_desc', $attr);
$attribute = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'simishow_gift_amount_desc'));
$attribute->addData($attr)->save();

$attr['type'] = 'text';
$attr['input'] = 'textarea';
$attr['label'] = 'Description of gift card value';
$attr['position'] = 5;
$attr['sort_order'] = 103;
$attr['backend_type'] = 'text';
$setup->addAttribute('catalog_product', 'simigift_amount_desc', $attr);
$attribute = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'simigift_amount_desc'));
$attribute->addData($attr)->save();

$attr['label'] = 'Description of gift card conditions';
$attr['position'] = 7;
$attr['sort_order'] = 105;
$setup->addAttribute('catalog_product', 'simigiftcard_description', $attr);
$attribute = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'simigiftcard_description'));
$attribute->addData($attr)->save();


/* update Gift Card Database */
$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher_history'), 'balance', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher_history'), 'customer_id', 'int(10) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher_history'), 'customer_email', 'varchar(255) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher'), 'conditions_serialized', 'mediumtext NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher'), 'day_to_send', 'date NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher'), 'is_sent', "tinyint(1) default '0'");
$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher'), 'shipped_to_customer', "tinyint(1) NOT NULL default '0'");
$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher'), 'created_form', 'varchar(45) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher'), 'template_id', 'int(10) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher'), 'description', 'text NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher'), 'giftvoucher_comments', 'text NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher'), 'email_sender', "tinyint(1) default '0'");

/* add fields for invoice */
$installer->getConnection()->addColumn(
    $installer->getTable('sales/invoice'), 'simibase_gift_voucher_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/invoice'), 'simigift_voucher_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/invoice'), 'simibase_use_gift_credit_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/invoice'), 'simiuse_gift_credit_amount', 'decimal(12,4) NULL');

/* add fields for creditmemo */
$installer->getConnection()->addColumn(
    $installer->getTable('sales/creditmemo'), 'simibase_gift_voucher_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/creditmemo'), 'simigift_voucher_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/creditmemo'), 'simibase_use_gift_credit_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/creditmemo'), 'simiuse_gift_credit_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/creditmemo'), 'simigiftcard_refund_amount', 'decimal(12,4) NULL');


/* add gift card credit database */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('simigiftvoucher_credit')};
DROP TABLE IF EXISTS {$this->getTable('simigiftvoucher_customer_voucher')};
DROP TABLE IF EXISTS {$this->getTable('simigiftvoucher_credit_history')};
DROP TABLE IF EXISTS {$this->getTable('simigiftvoucher_template')};
DROP TABLE IF EXISTS {$this->getTable('simigiftvoucher_product')};

CREATE TABLE {$this->getTable('simigiftvoucher_credit')} (
  `credit_id` int(10) unsigned NOT NULL auto_increment,
  `customer_id` int(10) unsigned NOT NULL ,
  `balance` decimal(12,4) default '0',
  `currency` varchar(45) default '',
  PRIMARY KEY (`credit_id`),
  INDEX (`customer_id`),
  FOREIGN KEY (`customer_id`)
  REFERENCES {$this->getTable('customer_entity')} (`entity_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('simigiftvoucher_credit_history')} (
  `history_id` int(10) unsigned NOT NULL auto_increment,
  `customer_id` int(10) unsigned NOT NULL ,
  `action` varchar(45) default '',
  `currency_balance` decimal(12,4) default '0',
  `giftcard_code` varchar(255) NOT NULL default '',
  `order_id` int(10) NULL ,
  `order_number` varchar(50) default '',
  `balance_change` decimal(12,4) default '0',
  `currency` varchar(45) default '',
  `base_amount` decimal(12,4) default '0',
  `amount` decimal(12,4) default '0',
  `created_date` datetime NULL,
  PRIMARY KEY (`history_id`),
  INDEX (`customer_id`),
  FOREIGN KEY (`customer_id`)
  REFERENCES {$this->getTable('customer_entity')} (`entity_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('simigiftvoucher_customer_voucher')} (
  `customer_voucher_id` int(10) unsigned NOT NULL auto_increment,
  `customer_id` int(10) unsigned NOT NULL ,
  `voucher_id` int(10) unsigned NOT NULL ,
  `added_date` datetime NULL,
  PRIMARY KEY (`customer_voucher_id`),
  INDEX (`customer_id`),
  INDEX (`voucher_id`),
  FOREIGN KEY (`customer_id`)
  REFERENCES {$this->getTable('customer_entity')} (`entity_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  FOREIGN KEY (`voucher_id`)
  REFERENCES {$this->getTable('simigiftvoucher')} (`giftvoucher_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('simigiftvoucher_template')} (
  `template_id` int(10) unsigned NOT NULL auto_increment,
  `type` varchar(45) default '',
  `template_name` varchar(255) default '',
  `pattern` varchar(255) default '',
  `balance` decimal(12,4) default '0',
  `currency` varchar(45) default '',
  `expired_at` datetime NULL,
  `amount` int(10) default '0',
  `day_to_send` datetime NULL,
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `conditions_serialized` mediumtext default '',
  `is_generated` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('simigiftvoucher_product')} (
  `giftcard_product_id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL,
  `conditions_serialized` mediumtext NULL,
  PRIMARY KEY (`giftcard_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    ");

$installer->endSetup();
