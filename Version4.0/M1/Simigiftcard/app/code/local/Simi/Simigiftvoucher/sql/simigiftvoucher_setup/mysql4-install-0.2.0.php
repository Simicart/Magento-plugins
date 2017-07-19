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

$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('catalog_setup');
$installer->startSetup();

$setup->removeAttribute('catalog_product', 'simigift_amount');
$attr = array(
    'group' => 'Prices',
    'type' => 'text',
    'input' => 'textarea',
    'label' => 'Gift amount',
    'backend' => '',
    'frontend' => '',
    'source' => '',
    'visible' => 1,
    'user_defined' => 1,
    'used_for_price_rules' => 1,
    'position' => 2,
    'unique' => 0,
    'default' => '',
    'sort_order' => 101,
);
$setup->addAttribute('catalog_product', 'simigift_amount', $attr);

$giftAmount = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'simigift_amount'));
$giftAmount->addData(array(
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required' => 0,
    'apply_to' => array('simigiftvoucher'),
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
    'backend_type' => 'text',
))->save();

$tax = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'tax_class_id'));
$applyTo = explode(',', $tax->getData('apply_to'));
$applyTo[] = 'simigiftvoucher';
$tax->addData(array('apply_to' => $applyTo))->save();

$weight = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'weight'));
$applyTo = explode(',', $weight->getData('apply_to'));
$applyTo[] = 'simigiftvoucher';
$weight->addData(array('apply_to' => $applyTo))->save();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('simigiftvoucher_history')};
DROP TABLE IF EXISTS {$this->getTable('simigiftvoucher')};

CREATE TABLE {$this->getTable('simigiftvoucher')} (
  `giftvoucher_id` int(11) unsigned NOT NULL auto_increment,
  `gift_code` varchar(127) NOT NULL default '',
  `balance` decimal(12,4) default '0',
  `currency` char(3) default '',
  `status` smallint(6) NOT NULL default '0',
  `expired_at` datetime NULL,
  `customer_id` int(11) unsigned default '0',
  `customer_name` varchar(127) NOT NULL default '',
  `customer_email` varchar(127) NOT NULL default '',
  `recipient_name` varchar(127) NOT NULL default '',
  `recipient_email` varchar(127) NOT NULL default '',
  `recipient_address` text default '',
  `message` text default '',
  `store_id` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY (`giftvoucher_id`),
  UNIQUE (`gift_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('simigiftvoucher_history')} (
  `history_id` int(11) unsigned NOT NULL auto_increment,
  `giftvoucher_id` int(11) unsigned NOT NULL,
  `action` smallint(6) NOT NULL default '0',
  `created_at` datetime NULL,
  `amount` decimal(12,4) default '0',
  `currency` char(3) default '',
  `status` smallint(6) NOT NULL default '0',
  `comments` text default '',
  `order_increment_id` int(11) unsigned,
  `order_item_id` int(11) unsigned,
  `order_amount` decimal(12,4) default '0',
  `extra_content` text default '',
  PRIMARY KEY (`history_id`),
  INDEX (`giftvoucher_id`),
  FOREIGN KEY (`giftvoucher_id`)
  REFERENCES {$this->getTable('simigiftvoucher')} (`giftvoucher_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");


$installer->endSetup();
