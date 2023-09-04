<?php

namespace Sivajik34\CustomFee\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();

        $quoteAddressTable = 'quote_address';
        $quoteTable = 'quote';
        $orderTable = 'sales_order';
        $invoiceTable = 'sales_invoice';
        $creditmemoTable = 'sales_creditmemo';
        $salesruleTable = 'salesrule';

        //Setup two columns for quote, quote_address and order
        //Quote address tables
        $setup->getConnection()
                ->addColumn(
                        $setup->getTable($quoteAddressTable), 'fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Fee'
                        ]
        );
        $setup->getConnection()
                ->addColumn(
                        $setup->getTable($quoteAddressTable), 'base_fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Base Fee'
                        ]
        );
        //Quote tables
        $setup->getConnection()
                ->addColumn(
                        $setup->getTable($quoteTable), 'fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Fee'
                        ]
        );

        $setup->getConnection()
                ->addColumn(
                        $setup->getTable($quoteTable), 'base_fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Base Fee'
                        ]
        );
        //Order tables
        $setup->getConnection()
                ->addColumn(
                        $setup->getTable($orderTable), 'fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Fee'
                        ]
        );

        $setup->getConnection()
                ->addColumn(
                        $setup->getTable($orderTable), 'base_fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Base Fee'
                        ]
        );
        //Invoice tables
        $setup->getConnection()
                ->addColumn(
                        $setup->getTable($invoiceTable), 'fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Fee'
                        ]
        );
        $setup->getConnection()
                ->addColumn(
                        $setup->getTable($invoiceTable), 'base_fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Base Fee'
                        ]
        );
        //Credit memo tables
        $setup->getConnection()
                ->addColumn(
                        $setup->getTable($creditmemoTable), 'fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Fee'
                        ]
        );
        $setup->getConnection()
                ->addColumn(
                        $setup->getTable($creditmemoTable), 'base_fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Base Fee'
                        ]
        );
        $setup->getConnection()
                ->addColumn(
                        $setup->getTable($salesruleTable), 'extra_fee_amount', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'comment' => 'Extra Fee Amount',
                    '10,2',
                    'default' => 0.00,
                    'nullable' => false,
                    'scale' => 4,
                    'precision' => 12,
                        ]
        );
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $setup->getConnection()->addColumn(
                    $setup->getTable('sales_order'), 'handling_charges', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Handling Charges'
                    ]
            );
            $setup->getConnection()->addColumn(
                    $setup->getTable('sales_order_item'), 'handling_charges', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Handling Charges'
                    ]
            );
            $setup->getConnection()->addColumn(
                    $setup->getTable('sales_order'), 'cut_to_length_charges', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Cut To Length Charges'
                    ]
            );
            $setup->getConnection()->addColumn(
                    $setup->getTable('sales_order_item'), 'cut_to_length_charges', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Cut To Length Charges'
                    ]
            );

            $setup->getConnection()->addColumn(
                    $setup->getTable('quote'), 'handling_charges', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Handling Charges'
                    ]
            );
            $setup->getConnection()->addColumn(
                    $setup->getTable('quote_item'), 'handling_charges', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Handling Charges'
                    ]
            );
            $setup->getConnection()->addColumn(
                    $setup->getTable('quote'), 'cut_to_length_charges', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Cut To Length Charges'
                    ]
            );
            $setup->getConnection()->addColumn(
                    $setup->getTable('quote_item'), 'cut_to_length_charges', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Cut To Length Charges'
                    ]
            );
        }
        $setup->endSetup();
    }
}
