<?php

namespace Exinent\Sales\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '2.1.3') < 0) {

            $connection = $installer->getConnection();

            if ($connection->tableColumnExists('quote', 'partial_shipment') === false) {
                $connection
                        ->addColumn(
                                $setup->getTable('quote'), 'partial_shipment', [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            'length' => 255,
                            'nullable' => true,
                            'comment' => 'Partial Shipment'
                                ]
                );
            }

            if ($connection->tableColumnExists('sales_order', 'partial_shipment') === false) {
                $connection
                        ->addColumn(
                                $setup->getTable('sales_order'), 'partial_shipment', [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            'length' => 255,
                            'nullable' => true,
                            'comment' => 'Partial Shipment'
                                ]
                );
            }

            if ($connection->tableColumnExists('sales_order_grid', 'partial_shipment') === false) {
                $connection
                        ->addColumn(
                                $setup->getTable('sales_order_grid'), 'partial_shipment', [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            'length' => 255,
                            'nullable' => true,
                            'comment' => 'Partial Shipment'
                                ]
                );
            }
        }

        $installer->endSetup();
    }

}
