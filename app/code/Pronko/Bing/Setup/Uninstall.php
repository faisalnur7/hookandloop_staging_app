<?php
/**
 * Copyright © Pronko Consulting (https://www.pronkoconsulting.com)
 * See LICENSE for license details.
 */
namespace Pronko\Bing\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @param SchemaSetupInterface
     * @param ModuleContextInterface
     *
     * @return void
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $connection = $setup->getConnection();

        $connection->delete(
            $setup->getTable('core_config_data'),
            ['path LIKE ?' => 'pronko_bing/%']
        );
    }
}