<?php
namespace Simi\Simibraintree\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->installSql($setup, $context);
    }

    public function installSql(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $context;
        $installer = $setup;
        $installer->startSetup();
        $installer->endSetup();
    }
}