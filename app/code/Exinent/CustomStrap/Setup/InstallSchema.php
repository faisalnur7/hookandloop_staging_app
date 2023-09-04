<?php

namespace Exinent\CustomStrap\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()
                ->newTable($installer->getTable('custom_strap_product'))
                ->addColumn('entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'identity' => true,
                        ], 'Entity ID')
                ->addColumn('sku', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                    'nullable' => false,
                    'default' => '',
                        ], 'Sku')
                ->addColumn('brand', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                    'nullable' => false,
                    'default' => '',
                        ], 'Brand')
                ->addColumn('strap_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                    'nullable' => false,
                    'default' => '',
                        ], 'Strap Type')
                ->addColumn('width', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                    'nullable' => false,
                    'default' => '',
                        ], 'Width')
                ->addColumn('length', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                    'nullable' => false,
                    'default' => '',
                        ], 'Length')
                ->addColumn('price', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '10,2', [
                    'nullable' => false,
                        ], 'Price')
                ->setComment('Custom Strap Product table');
        $installer->getConnection()->createTable($table);
    }
}
