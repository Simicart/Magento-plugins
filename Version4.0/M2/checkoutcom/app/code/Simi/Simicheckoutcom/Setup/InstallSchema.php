<?php
/**
 * Copyright © 2016 Simi. All rights reserved.
 */

namespace Simi\Simicheckoutcom\Setup;

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

        $table_key_name = $installer->getTable('simicheckoutcom');
        if ($installer->getConnection()->isTableExists($table_key_name) == true) {
            $installer->getConnection()->dropTable($installer->getConnection()->getTableName('simicheckoutcom'));
        }
        $table_key = $installer->getConnection()->newTable(
            $table_key_name
        )->addColumn(
            'simicheckoutcom_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Checkout.com Id'
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
            'currency_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Currency Code'
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
