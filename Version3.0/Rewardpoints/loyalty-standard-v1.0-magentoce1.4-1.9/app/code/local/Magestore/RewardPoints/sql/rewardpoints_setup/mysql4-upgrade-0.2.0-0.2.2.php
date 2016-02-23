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

    $installer->getConnection()->addColumn($this->getTable('sales/order'), 'rewardpoints_base_amount', 'decimal(12,4) NOT NULL default 0');
    $installer->getConnection()->addColumn($this->getTable('sales/order'), 'rewardpoints_amount', 'decimal(12,4) NOT NULL default 0');
	
$installer->endSetup();
