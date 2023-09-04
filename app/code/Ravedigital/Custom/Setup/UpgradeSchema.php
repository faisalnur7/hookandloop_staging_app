<?php
namespace Ravedigital\Custom\Setup;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $quoteTable = 'quote';
        //Quote table
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteTable),
                'original_items_qty',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'default' => 0,
                    'nullable' => true,
                    'comment' => 'Original Items Quantity'
                ]
            );
       
        $setup->endSetup();
    }
}
