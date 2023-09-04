<?php

namespace Exinent\TaxExempt\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {

            $connection = $installer->getConnection();

            if ($connection->tableColumnExists('quote_payment', 'tax_relief_code') === false) {
                $connection
                        ->addColumn(
                                $setup->getTable('quote_payment'), 'tax_relief_code', [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            'length' => 255,
                            'nullable' => true,
                            'comment' => 'Tax Relief Code'
                                ]
                );
            }
            if ($connection->tableColumnExists('quote_payment', 'tax_relief_state') === false) {
                $connection
                        ->addColumn(
                                $setup->getTable('quote_payment'), 'tax_relief_state', [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            'length' => 255,
                            'nullable' => true,
                            'comment' => 'Tax Relief State'
                                ]
                );
            }
            if ($connection->tableColumnExists('sales_order_payment', 'tax_relief_code') === false) {
                $connection
                        ->addColumn(
                                $setup->getTable('sales_order_payment'), 'tax_relief_code', [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            'length' => 255,
                            'nullable' => true,
                            'comment' => 'Tax Relief Code'
                                ]
                );
            }
            if ($connection->tableColumnExists('sales_order_payment', 'tax_relief_state') === false) {
                $connection
                        ->addColumn(
                                $setup->getTable('sales_order_payment'), 'tax_relief_state', [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            'length' => 255,
                            'nullable' => true,
                            'comment' => 'Tax Relief State'
                                ]
                );
            }
        }

        $installer->endSetup();
    }

}
