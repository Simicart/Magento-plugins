<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Rewardpoints
 * @copyright   Copyright (c) 2012 
 * @license     
 */
/** @var $installer Magestore_RewardPointsRule_Model_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create rewardpointsrule table
 */
$installer->getConnection()->addColumn($this->getTable('rewardpoints/rate'), 'max_price_spended_type', 'VARCHAR(15) NULL');
$installer->getConnection()->addColumn($this->getTable('rewardpoints/rate'), 'max_price_spended_value', 'DECIMAL(12,4) NULL');
//$installer->run("
//ALTER TABLE {$this->getTable('rewardpoints_rate')}
//  ADD COLUMN `max_price_spended_type` VARCHAR(15) NULL,
//  ADD COLUMN `max_price_spended_value` DECIMAL(12,4) NULL;
//
//");

$installer->endSetup();
