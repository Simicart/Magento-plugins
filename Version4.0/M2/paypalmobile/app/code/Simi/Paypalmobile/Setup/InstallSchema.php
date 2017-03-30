<?php
/**
 * Copyright © 2016 Simi. All rights reserved.
 */

namespace Simi\Paypalmobile\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
	
        $installer = $setup;
        $installer->startSetup();

        $table_key_name = $installer->getTable('paypalmobile');
        if ($installer->getConnection()->isTableExists($table_key_name) == true) {
            $installer->getConnection()->dropTable($installer->getConnection()->getTableName('paypalmobile'));
        }
        $table_key = $installer->getConnection()->newTable(
            $table_key_name
        )->addColumn(
            'paypalmobile_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Paypal Mobile Id'
        )->addColumn(
            'transaction_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Transaction Id'
        )->addColumn(
            'transaction_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Transaction Name'
        )->addColumn(
            'transaction_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Transaction Email'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Status'
        )->addColumn(
            'amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Amount'
        )->addColumn(
            'currency_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Currency Code'
        )->addColumn(
            'transaction_dis',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'transaction dis'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Order Id'
        );
        
        $installer->getConnection()->createTable($table_key);
		
        $installer->endSetup();

    }
}
