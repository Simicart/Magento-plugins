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

$installer = Mage::getResourceModel('catalog/setup','catalog_setup');
/* @var $installer Mage_Core_Model_Resource_Setup */


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
  `order_increment_id` varchar(127) default '',
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

$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher'), 'notify_success', "tinyint(1) default '0'");
$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher'), 'giftcard_custom_image', "tinyint(1) default '0'");
$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher'), 'giftcard_template_id', "int(11) default '0'");
$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher'), 'giftcard_template_image', "varchar(255) NULL");
$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher_product'), 'simigiftcard_description', "text(500) default NULL");
$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher_product'), 'actions_serialized', 'mediumtext NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('simigiftvoucher'), 'actions_serialized', 'mediumtext NULL');

$installer->getConnection()->addColumn(
    $installer->getTable('sales/order'), 'simibase_giftvoucher_discount_for_shipping', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/order'), 'simigiftvoucher_discount_for_shipping', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/order'), 'simibase_giftcredit_discount_for_shipping', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/order'), 'simigiftcredit_discount_for_shipping', 'decimal(12,4) NULL');
/* add fields for order item */
$installer->getConnection()->addColumn(
    $installer->getTable('sales/order_item'), 'simibase_gift_voucher_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/order_item'), 'simigift_voucher_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/order_item'), 'simibase_use_gift_credit_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/order_item'), 'simiuse_gift_credit_amount', 'decimal(12,4) NULL');

/* add fields for invoice item */
$installer->getConnection()->addColumn(
    $installer->getTable('sales/invoice_item'), 'simibase_gift_voucher_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/invoice_item'), 'simigift_voucher_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/invoice_item'), 'simibase_use_gift_credit_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/invoice_item'), 'simiuse_gift_credit_amount', 'decimal(12,4) NULL');

/* add fields for creditmemo item */
$installer->getConnection()->addColumn(
    $installer->getTable('sales/creditmemo_item'), 'simibase_gift_voucher_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/creditmemo_item'), 'simigift_voucher_discount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/creditmemo_item'), 'simibase_use_gift_credit_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/creditmemo_item'), 'simiuse_gift_credit_amount', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn(
    $installer->getTable('sales/creditmemo_item'), 'simigiftcard_refund_amount', 'decimal(12,4) NULL');
$installer->run("
    
DROP TABLE IF EXISTS {$this->getTable('simigiftcard_template')};    
CREATE TABLE {$this->getTable('simigiftcard_template')} (
  `giftcard_template_id` int(11) unsigned NOT NULL auto_increment,
  `template_name` varchar(255) NOT NULL,
  `style_color` varchar(255) NOT NULL,
  `text_color` varchar(255) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `notes` text(500) NULL,
  `images` text NULL,
  `design_pattern` smallint(5),
  `background_img` varchar(255) NULL,
  `status` smallint NOT NULL default 1,  
  PRIMARY KEY (`giftcard_template_id`)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('simigiftvoucher')}
    ADD CONSTRAINT `FK_SIMIGIFTVOUCHER_RELATION_TEMPLATE` FOREIGN KEY (`giftcard_template_id`)
    REFERENCES {$this->getTable('simigiftcard_template')} (`giftcard_template_id`)
        ON DELETE CASCADE;
");



$setup = new Mage_Eav_Model_Entity_Setup('catalog_setup');
$installer->startSetup();
$setup->removeAttribute('catalog_product', 'simigift_amount');
$setup->removeAttribute('catalog_product', 'simigift_type');
$setup->removeAttribute('catalog_product', 'simishow_gift_amount_desc');
$setup->removeAttribute('catalog_product', 'simigift_amount_desc');
$setup->removeAttribute('catalog_product', 'simigiftcard_description');
$setup->removeAttribute('catalog_product', 'simigift_value');
$setup->removeAttribute('catalog_product', 'simigift_from');
$setup->removeAttribute('catalog_product', 'simigift_to');
$setup->removeAttribute('catalog_product', 'simigift_dropdown');
$setup->removeAttribute('catalog_product', 'simigift_price_type');
$setup->removeAttribute('catalog_product', 'simigift_price');
$setup->removeAttribute('catalog_product', 'simigift_template_ids');
/**
 * add gift template attribute
 */
$attGiftTemplate = array(
    'group' => 'General',
    'type' => 'varchar',
    'input' => 'multiselect',
    'default' => 1,
    'label' => 'Select Gift Card templates ',
    'backend' => 'eav/entity_attribute_backend_array',
    'frontend' => '',
    'source' => 'simigiftvoucher/templateoptions',
    'visible' => 1,
    'user_defined' => 1,
    'used_for_price_rules' => 1,
    'position' => 2,
    'unique' => 0,
    'default' => '',
    'sort_order' => 100,
    'apply_to' => array('simigiftvoucher'),
);
$setup->addAttribute('catalog_product', 'simigift_template_ids', $attGiftTemplate);
$attGiftTemplate = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'simigift_template_ids'));
$attGiftTemplate->addData(array(
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required' => 1,
    'apply_to' => array('simigiftvoucher'),
    'is_configurable' => 1,
    'is_searchable' => 0,
    'is_visible_in_advanced_search' => 0,
    'is_comparable' => 0,
    'is_filterable' => 0,
    'is_filterable_in_search' => 1,
    'is_used_for_promo_rules' => 1,
    'is_html_allowed_on_front' => 0,
    'is_visible_on_front' => 0,
    'used_in_product_listing' => 1,
    'used_for_sort_by' => 0,
))->save();
/**
 * add gift type attribute
 */
$attGifttype = array(
    'group' => 'Prices',
    'type' => 'int',
    'input' => 'select',
    'label' => 'Type of Gift Card value',
    'backend' => '',
    'frontend' => '',
    'source' => 'simigiftvoucher/gifttype',
    'visible' => 1,
    'user_defined' => 1,
    'used_for_price_rules' => 1,
    'position' => 2,
    'unique' => 0,
    'default' => '',
    'sort_order' => 101,
    'apply_to' => array('simigiftvoucher'),
);
$setup->addAttribute('catalog_product', 'simigift_type', $attGifttype);
$giftType = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'simigift_type'));
$giftType->addData(array(
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required' => 1,
    'apply_to' => array('simigiftvoucher'),
    'is_configurable' => 1,
    'is_searchable' => 0,
    'is_visible_in_advanced_search' => 0,
    'is_comparable' => 0,
    'is_filterable' => 0,
    'is_filterable_in_search' => 1,
    'is_used_for_promo_rules' => 1,
    'is_html_allowed_on_front' => 0,
    'is_visible_on_front' => 0,
    'used_in_product_listing' => 1,
    'used_for_sort_by' => 0,
))->save();
/**
 * add simigift_value attribute
 */
$attGiftValue = array(
    'group' => 'Prices',
    'type' => 'decimal',
    'input' => 'price',
    'class' => 'validate-number',
    'label' => 'Gift Card value',
    'backend' => '',
    'frontend' => '',
    'source' => '',
    'visible' => 1,
    'user_defined' => 1,
    'used_for_price_rules' => 1,
    'position' => 4,
    'unique' => 0,
    'default' => '',
    'sort_order' => 103,
);
$setup->addAttribute('catalog_product', 'simigift_value', $attGiftValue);
$giftValue = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'simigift_value'));
$giftValue->addData(array(
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required' => 1,
    'apply_to' => array('simigiftvoucher'),
    'is_configurable' => 1,
    'is_searchable' => 0,
    'is_visible_in_advanced_search' => 0,
    'is_comparable' => 0,
    'is_filterable' => 0,
    'is_filterable_in_search' => 1,
    'is_used_for_promo_rules' => 1,
    'is_html_allowed_on_front' => 0,
    'is_visible_on_front' => 0,
    'used_in_product_listing' => 1,
    'used_for_sort_by' => 0,
))->save();
/**
 * add simigift_price attribute
 */
$attGiftPrice = array(
    'group' => 'Prices',
    'type' => 'text',
    'input' => 'text',
    'label' => 'Gift Card price',
    'backend' => '',
    'frontend' => '',
    'source' => '',
    'visible' => 1,
    'user_defined' => 1,
    'used_for_price_rules' => 1,
    'position' => 8,
    'unique' => 0,
    'default' => '',
    'sort_order' => 105,
    'is_required' => 1,
    'note' => 'Notes: ',
);
$setup->addAttribute('catalog_product', 'simigift_price', $attGiftPrice);
$giftPrice = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'simigift_price'));
$giftPrice->addData(array(
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required' => 1,
    'apply_to' => array('simigiftvoucher'),
    'is_configurable' => 1,
    'is_searchable' => 0,
    'is_visible_in_advanced_search' => 0,
    'is_comparable' => 0,
    'is_filterable' => 0,
    'is_filterable_in_search' => 1,
    'is_used_for_promo_rules' => 1,
    'is_html_allowed_on_front' => 0,
    'is_visible_on_front' => 0,
    'used_in_product_listing' => 1,
    'used_for_sort_by' => 0,
))->save();

/* add Gift Card product attribute */
//show description of giftcard
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
    'position' => 10,
    'unique' => 0,
    'default' => '',
    'sort_order' => 109,
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required' => 0,
    'apply_to' => 'simigiftvoucher',
    'is_configurable' => 1,
    'is_searchable' => 0,
    'is_visible_in_advanced_search' => 0,
    'is_comparable' => 0,
    'is_filterable' => 0,
    'is_filterable_in_search' => 1,
    'is_used_for_promo_rules' => 1,
    'is_html_allowed_on_front' => 0,
    'is_visible_on_front' => 0,
    'used_in_product_listing' => 1,
    'used_for_sort_by' => 0,
);

/**
 * add gift from,to attribute for gift type range
 */
$attr['type'] = 'decimal';
$attr['input'] = 'price';
$attr['is_required'] = 1;
$attr['label'] = 'Minimum Gift Card value';
$attr['position'] = 4;
$attr['sort_order'] = 102;
$attr['class'] = 'validate-number';
$setup->addAttribute('catalog_product', 'simigift_from', $attr);
$attribute = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'simigift_from'));
$attribute->addData($attr)->save();
$attr['type'] = 'decimal';
$attr['input'] = 'price';
$attr['label'] = 'Maximum Gift Card value';
$attr['position'] = 5;
$attr['sort_order'] = 103;
$attr['class'] = 'validate-number';
$setup->addAttribute('catalog_product', 'simigift_to', $attr);
$attribute = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'simigift_to'));
$attribute->addData($attr)->save();
/**
 * add gift value attribute for gift type dropdown
 */
$attr['type'] = 'varchar';
$attr['input'] = 'text';
$attr['label'] = 'Gift Card values';
$attr['position'] = 6;
$attr['sort_order'] = 102;
$attr['backend_type'] = 'text';
$attr['class'] = '';
$attr['note'] = Mage::helper('simigiftvoucher')->__('Seperated by comma, e.g. 10,20,30');
$setup->addAttribute('catalog_product', 'simigift_dropdown', $attr);
$attribute = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'simigift_dropdown'));
$attribute->addData($attr)->save();
//gift price type
$attr['type'] = 'int';
$attr['is_required'] = 0;
$attr['input'] = 'select';
$attr['source'] = 'simigiftvoucher/giftpricetype';
$attr['label'] = 'Type of Gift Card price';
$attr['position'] = 7;
$attr['sort_order'] = 104;
$attr['backend_type'] = 'text';
$attr['note'] = 'Gift Card price is the same as Gift Card value by default.';
$attr['class'] = '';
$setup->addAttribute('catalog_product', 'simigift_price_type', $attr);
$attribute = Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product', 'simigift_price_type'));
$attribute->addData($attr)->save();

$installer->getConnection()->addColumn(
    $this->getTable('sales/order'), 'simibase_gift_voucher_discount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/order'), 'simigift_voucher_discount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/order'), 'simibase_use_gift_credit_amount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/order'), 'simiuse_gift_credit_amount', 'decimal(12,4) NOT NULL default 0');

$installer->getConnection()->addColumn(
    $this->getTable('sales/order'), 'simigiftvoucher_base_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/order'), 'simigiftvoucher_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/order'), 'simigiftcredit_base_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/order'), 'simigiftcredit_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');

$installer->getConnection()->addColumn(
    $this->getTable('sales/order_item'), 'simigiftvoucher_base_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/order_item'), 'simigiftvoucher_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/order_item'), 'simigiftcredit_base_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/order_item'), 'simigiftcredit_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');

$installer->getConnection()->addColumn(
    $this->getTable('sales/invoice'), 'simigiftvoucher_base_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/invoice'), 'simigiftvoucher_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/invoice'), 'simigiftcredit_base_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/invoice'), 'simigiftcredit_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');

$installer->getConnection()->addColumn(
    $this->getTable('sales/invoice'), 'simigift_voucher_discount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/invoice'), 'simibase_gift_voucher_discount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/invoice'), 'simibase_use_gift_credit_amount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/invoice'), 'simiuse_gift_credit_amount', 'decimal(12,4) NOT NULL default 0');

$installer->getConnection()->addColumn(
    $this->getTable('sales/creditmemo'), 'simigiftvoucher_base_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/creditmemo'), 'simigiftvoucher_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/creditmemo'), 'simigiftcredit_base_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/creditmemo'), 'simigiftcredit_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');

$installer->getConnection()->addColumn(
    $this->getTable('sales/order'), 'simigiftvoucher_base_shipping_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/order'), 'simigiftvoucher_shipping_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/order'), 'simigiftcredit_base_shipping_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');
$installer->getConnection()->addColumn(
    $this->getTable('sales/order'), 'simigiftcredit_shipping_hidden_tax_amount', 'decimal(12,4) NOT NULL default 0');

$installer->getConnection()->addColumn(
    $this->getTable('simigiftvoucher/template'), 'giftcard_template_id', "int(11) NOT NULL");
$installer->getConnection()->addColumn(
    $this->getTable('simigiftvoucher/template'), 'giftcard_template_image', "varchar(255) NULL");
$installer->getConnection()->addColumn(
    $this->getTable('simigiftvoucher'), 'timezone_to_send', "text(100) default NULL");
$installer->getConnection()->addColumn(
    $this->getTable('simigiftvoucher'), 'day_store', 'datetime NULL');

$model = Mage::getModel('simigiftvoucher/gifttemplate');
//Simple template
$data = array();


$data[0]['template_name'] = Mage::helper('simigiftvoucher')->__('Amazon Gift Card Style');
$data[0]['style_color'] = '#DC8C71';
$data[0]['text_color'] = '#949392';
$data[0]['caption'] = Mage::helper('simigiftvoucher')->__('Gift Card');
$data[0]['notes'] = '';
$data[0]['images'] = 'default.png,giftcard_amazon_01.png,giftcard_amazon_02.png,giftcard_amazon_03.png,'
    . 'giftcard_amazon_04.png,giftcard_amazon_05.png,giftcard_amazon_06.png,giftcard_amazon_07.png,'
    . 'giftcard_amazon_08.png,giftcard_amazon_09.png,giftcard_amazon_10.png,giftcard_amazon_11.png,'
    . 'giftcard_amazon_12.png,giftcard_amazon_13.png,giftcard_amazon_14.png,giftcard_amazon_15.png,'
    . 'giftcard_amazon_16.png,giftcard_amazon_17.png,giftcard_amazon_18.png';
$data[0]['design_pattern'] = Simi_Simigiftvoucher_Model_Designpattern::PATTERN_AMAZON;

foreach ($data as $template) {
    $model->setData($template);
    try {
        $model->save();
    } catch (Exception $exc) {

    }
}

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('simigiftvoucher_giftcodeset')};

CREATE TABLE {$this->getTable('simigiftvoucher_giftcodeset')} (
  `set_id` int(11) unsigned NOT NULL auto_increment,
  `set_name` varchar(45) NOT NULL default '',
  `set_qty` int(11) NULL default 0,
  PRIMARY KEY (`set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");
$installer->getConnection()->addColumn(
    $this->getTable('simigiftvoucher/giftvoucher'), 'set_id', "int(11) NULL");
$installer->getConnection()->addColumn(
    $this->getTable('simigiftvoucher/giftvoucher'), 'used', "smallint(1) NULL");
$data = array(
    'group' => 'General',
    'type' => 'varchar',
    'input' => 'select',
    'label' => 'Select The Gift Code Sets ',
    'backend' => '',
    'frontend' => '',
    'source' => 'Simi_Simigiftvoucher_Model_GiftCodeSetOptions',
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'used_for_price_rules' => 1,
    'position' => 2,
    'unique' => 0,
    'default' => '',
    'sort_order' => 100,
    'apply_to' => 'simigiftvoucher',
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_required' => 0,
    'is_configurable' => 1,
    'is_searchable' => 0,
    'is_visible_in_advanced_search' => 0,
    'is_comparable' => 0,
    'is_filterable' => 0,
    'is_filterable_in_search' => 1,
    'is_used_for_promo_rules' => 1,
    'is_html_allowed_on_front' => 0,
    'is_visible_on_front' => 0,
    'used_in_product_listing' => 1,
    'used_for_sort_by' => 0,
);
$installer->addAttribute('catalog_product', 'simigift_code_sets', $data);

Mage::getConfig()->saveConfig('simigiftvoucher/email/show_only_simple_template', 1);

$installer->endSetup();
