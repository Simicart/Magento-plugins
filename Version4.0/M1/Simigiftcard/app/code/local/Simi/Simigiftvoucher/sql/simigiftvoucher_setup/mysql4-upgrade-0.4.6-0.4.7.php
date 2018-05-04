<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 1/19/18
 * Time: 10:15 AM
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('simigiftvoucher'), 'cart_rule_description', 'text  NULL default ""');

$installer->endSetup();