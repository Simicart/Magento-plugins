<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('simihr/jobOffers'))->addColumn('sort_order_id', Varien_Db_Ddl_Table::TYPE_INTEGER,null,  array(

    ), 'Sort order');

$installer->endSetup();
?>