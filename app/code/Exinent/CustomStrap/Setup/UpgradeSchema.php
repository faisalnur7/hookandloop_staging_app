<?php

/**
 * Voronoy_ExtraFee Module 
 *
 * @category    ExtraFee
 * @package     Voronoy_ExtraFee
 * @author      pawan
 *
 */

namespace Exinent\CustomStrap\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
         $installer = $setup;
        $installer->startSetup();

       if (version_compare($context->getVersion(), '0.1.2', '<')) {

            $tables = array(
                $installer->getTable('sales_order'),
                $installer->getTable('quote_item'),
            );
            //Declare data
            foreach ($tables as $table) {
                if ($installer->getConnection()->isTableExists($table) == true) {
                    $installer->getConnection()->addColumn($table, 'custom_strap_length', array(
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'length' => '255',
                        'comment' => 'Custom Strap Length',
                            )
                    );

                   
                }
            }
        }
       
        $installer->endSetup();
    }
}