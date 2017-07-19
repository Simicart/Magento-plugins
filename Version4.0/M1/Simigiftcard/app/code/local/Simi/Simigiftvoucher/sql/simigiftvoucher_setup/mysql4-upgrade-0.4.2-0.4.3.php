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
    $this->getTable('simigiftvoucher/template'), 'giftcard_template_id', "int(11) NOT NULL");
$installer->getConnection()->addColumn(
    $this->getTable('simigiftvoucher/template'), 'giftcard_template_image', "varchar(255) NULL");
$installer->getConnection()->addColumn(
    $this->getTable('simigiftvoucher'), 'timezone_to_send', "text(100) default NULL");
$installer->getConnection()->addColumn(
    $this->getTable('simigiftvoucher'), 'day_store', 'datetime NULL');

$installer->endSetup();
