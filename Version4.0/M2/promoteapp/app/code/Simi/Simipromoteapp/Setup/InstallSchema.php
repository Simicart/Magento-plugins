<?php


namespace Simi\Simipromoteapp\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public $simiObjectManager;
    public $contentHelper;
    
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {
        $this->simiObjectManager  = $simiObjectManager;
        $this->contentHelper      = $this->simiObjectManager->get('Simi\Simipromoteapp\Helper\Content');
        return $this;
    }
    
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->installSql($setup, $context);
    }
    
    public function installSql(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $context;
        $installer = $setup;
        $installer->startSetup();

        //Add Table
        
        $table_key_name = $installer->getTable('simipromoteapp');
        $this->checkTableExist($installer, $table_key_name, 'simipromoteapp');
        
        $table  = $installer->getConnection()
            ->newTable($table_key_name)
            ->addColumn(
                'simipromoteapp_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false],
                'Id'
            )->addColumn(
                'template_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'unsigned' => true],
                'Template Id'
            )->addColumn(
                'customer_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Customer Name'
            )->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Customer Email'
            )->addColumn(
                'is_open',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'unsigned' => true],
                'Is Open'
            )->addColumn(
                'created_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Created Time'
            )->addColumn(
                'update_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Updated Time'
            )->addIndex(
                'idx_primary',
                ['simipromoteapp_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY]
            );
        $installer->getConnection()->createTable($table);
        
        $installer->endSetup();
    }
    
    public function checkTableExist($installer, $table_key_name, $table_name)
    {
        if ($installer->getConnection()->isTableExists($table_key_name) == true) {
            $installer->getConnection()
                    ->dropTable($installer->getConnection()->getTableName($table_name));
        }
    }
}
