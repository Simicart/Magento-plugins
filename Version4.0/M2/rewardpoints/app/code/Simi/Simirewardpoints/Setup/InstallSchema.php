<?php

namespace Simi\Simirewardpoints\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Drop tables if exists
         */
        $installer->getConnection()->dropTable($installer->getTable('simirewardpoints_customer'));
        $installer->getConnection()->dropTable($installer->getTable('simirewardpoints_rate'));
        $installer->getConnection()->dropTable($installer->getTable('simirewardpoints_transaction'));
        /**
         * Create table 'simirewardpoints_customer'
         */
        $table = $installer->getConnection()
                ->newTable($installer->getTable('simirewardpoints_customer'))
                ->addColumn(
                    'reward_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true]
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false]
                )
                ->addColumn(
                    'point_balance',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0]
                )
                ->addColumn(
                    'holding_balance',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0]
                )
                ->addColumn(
                    'spent_balance',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0]
                )
                ->addColumn(
                    'is_notification',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false]
                )
                ->addColumn(
                    'expire_notification',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false]
                );
        $installer->getConnection()->createTable($table);
        /**
         * Create table 'rewardpoints_rate'
         */
        $table = $installer->getConnection()
                        ->newTable($installer->getTable('simirewardpoints_rate'))
                        ->addColumn(
                            'rate_id',
                            Table::TYPE_INTEGER,
                            null,
                            ['identity' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true]
                        )
                        ->addColumn(
                            'website_ids',
                            Table::TYPE_SMALLINT,
                            null,
                            ['nullable' => false, 'unsigned' => true]
                        )
                        ->addColumn(
                            'customer_group_ids',
                            Table::TYPE_TEXT,
                            null
                        )
                        ->addColumn(
                            'direction',
                            Table::TYPE_SMALLINT,
                            null,
                            ['nullable' => false]
                        )
                        ->addColumn(
                            'points',
                            Table::TYPE_INTEGER,
                            null,
                            ['nullable' => false, 'default' => 0]
                        )
                        ->addColumn(
                            'money',
                            Table::TYPE_DECIMAL,
                            null,
                            ['nullable' => false, 'default' => 0]
                        )
                        ->addColumn(
                            'max_price_spended_type',
                            Table::TYPE_TEXT,
                            null
                        )
                        ->addColumn(
                            'max_price_spended_value',
                            Table::TYPE_DECIMAL,
                            null
                        )
                        ->addColumn(
                            'status',
                            Table::TYPE_SMALLINT,
                            null,
                            ['nullable' => false, 'default' => 0]
                        )
                        ->addColumn(
                            'sort_order',
                            Table::TYPE_INTEGER,
                            null,
                            ['nullable' => false]
                        )->addForeignKey(
                            $installer->getFkName('simirewardpoints_rate', 'website_ids', 'store_website', 'website_id'),
                            'website_ids',
                            $installer->getTable('store_website'),
                            'website_id',
                            Table::ACTION_CASCADE
                        );
        $installer->getConnection()->createTable($table);
        /**
         * Create  'simirewardpoints_transaction' table
         */
        $table = $installer->getConnection()
                ->newTable($installer->getTable('simirewardpoints_transaction'))
                ->addColumn(
                    'transaction_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true]
                )
                ->addColumn(
                    'reward_id',
                    Table::TYPE_INTEGER,
                    null
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null
                )
                ->addColumn(
                    'customer_email',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false]
                )
                ->addColumn(
                    'title',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false]
                )
                ->addColumn(
                    'action',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false]
                )
                ->addColumn(
                    'action_type',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => 0]
                )
                ->addColumn(
                    'store_id',
                    Table::TYPE_SMALLINT,
                    null
                )
                ->addColumn(
                    'point_amount',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0]
                )
                ->addColumn(
                    'point_used',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0]
                )
                ->addColumn(
                    'real_point',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0]
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false]
                )
                ->addColumn(
                    'created_time',
                    Table::TYPE_DATETIME,
                    null
                )
                ->addColumn(
                    'updated_time',
                    Table::TYPE_DATETIME,
                    null
                )
                ->addColumn(
                    'expiration_date',
                    Table::TYPE_DATETIME,
                    null
                )
                ->addColumn(
                    'expire_email',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => 0]
                )
                ->addColumn(
                    'order_id',
                    Table::TYPE_INTEGER,
                    null
                )
                ->addColumn(
                    'order_increment_id',
                    Table::TYPE_TEXT,
                    null
                )
                ->addColumn(
                    'order_base_amount',
                    Table::TYPE_DECIMAL,
                    null
                )
                ->addColumn(
                    'order_amount',
                    Table::TYPE_DECIMAL,
                    null
                )
                ->addColumn(
                    'base_discount',
                    Table::TYPE_DECIMAL,
                    null
                )
                ->addColumn(
                    'discount',
                    Table::TYPE_DECIMAL,
                    null
                )
                ->addColumn(
                    'extra_content',
                    Table::TYPE_TEXT,
                    null
                );
        $installer->getConnection()->createTable($table);

        /**
         * Add more column to table  'quote_item'
         */
        $installer->getConnection()->addColumn(
            $installer->getTable('quote_item'),
            'simirewardpoints_base_discount',
            'decimal(12,4) NOT NULL default 0'
        );
        /**
         * Add more column to table  'quote_address'
         */
        $installer->getConnection()->addColumn(
            $installer->getTable('quote_address'),
            'simirewardpoints_base_amount',
            'decimal(12,4) NOT NULL default 0'
        );
        /**
         * Add more column to table  'sales_order'
         */
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'simirewardpoints_earn',
            'int(11) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'simirewardpoints_spent',
            'int(11) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'simirewardpoints_base_discount',
            'decimal(12,4) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'simirewardpoints_base_amount',
            'decimal(12,4) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'simirewardpoints_amount',
            'decimal(12,4) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'simirewardpoints_discount',
            'decimal(12,4) NOT NULL default 0'
        );
        /**
         * Add more column to table  'sales_order_item'
         */
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_item'),
            'simirewardpoints_earn',
            'int(11) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_item'),
            'simirewardpoints_spent',
            'int(11) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_item'),
            'simirewardpoints_base_discount',
            'decimal(12,4) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_item'),
            'simirewardpoints_discount',
            'decimal(12,4) NOT NULL default 0'
        );
        /**
         * Add more column to table  'sales_invoice'
         */
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_invoice'),
            'simirewardpoints_base_discount',
            'decimal(12,4) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_invoice'),
            'simirewardpoints_earn',
            'int(11) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_invoice'),
            'simirewardpoints_discount',
            'decimal(12,4) NOT NULL default 0'
        );
        /**
         * Add more column to table  'sales_creditmemo'
         */
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_creditmemo'),
            'simirewardpoints_base_discount',
            'decimal(12,4) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_creditmemo'),
            'simirewardpoints_earn',
            'int(11) NOT NULL default 0'
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_creditmemo'),
            'simirewardpoints_discount',
            'decimal(12,4) NOT NULL default 0'
        );
        /**
         * create rewardpoints_rule table
         */
        $table = $installer->getConnection()
                ->newTable($installer->getTable('simirewardpoints_rule'))
                ->addColumn(
                    'max_price_spended_type',
                    Table::TYPE_TEXT,
                    null
                )
                ->addColumn(
                    'max_price_spended_value',
                    Table::TYPE_DECIMAL,
                    null
                );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
