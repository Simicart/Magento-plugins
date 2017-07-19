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
$installer->endSetup();

