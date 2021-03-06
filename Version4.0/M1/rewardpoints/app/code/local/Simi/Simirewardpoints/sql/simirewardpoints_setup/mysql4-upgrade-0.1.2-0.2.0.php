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
 * @package     Simi_SimirewardpointsRule
 * @copyright   Copyright (c) 2012 Simi (http://www.simi.com/)
 * @license     http://www.simi.com/license-agreement.html
 */

/** @var $installer Simi_SimirewardpointsRule_Model_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

if (version_compare(Mage::getVersion(), '1.4.1.0', '>=')) {
    $installer->getConnection()->addColumn($this->getTable('sales/invoice'), 'simirewardpoints_earn', 'int(11) NOT NULL default 0');
    $installer->getConnection()->addColumn($this->getTable('sales/creditmemo'), 'simirewardpoints_earn', 'int(11) NOT NULL default 0');
} else {
    $setup = new Mage_Sales_Model_Mysql4_Setup('sales_setup');
    $setup->addAttribute('invoice', 'simirewardpoints_earn', array('type' => 'ịnt'));    
    $setup->addAttribute('creditmemo', 'simirewardpoints_earn', array('type' => 'int'));
}

$installer->endSetup();
