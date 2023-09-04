<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See https://www.mageworx.com/terms-and-conditions for license details.
 */
declare(strict_types=1);

namespace MageWorx\OptionDependency\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;


class ProcessDeleteOldData
{
    private ResourceConnection $resourceConnection;

    /**
     * ProductPathCollection constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    public function deleteOldData(
        array $productIds,
        array $groupIds,
        string $columnToCompare,
        bool $isAfetrTenmplateSave,
        string $tableName
    ): void {
        $connection = $this->resourceConnection->getConnection();

        $select = $connection->select()
                             ->reset()
                             ->from(['dep' => $this->resourceConnection->getTableName($tableName)])
                             ->joinLeft(
                                 ['cpo' => $this->resourceConnection->getTableName('catalog_product_option')],
                                 'cpo.option_id = ' . $columnToCompare,
                                 []
                             );
        if ($isAfetrTenmplateSave && $groupIds) {
            $select->where(
                "dep.group_id IN (" . implode(',', $groupIds) . ") AND " .
                "dep.product_id IN (" . implode(',', $productIds) . ")"
            );
        } else {
            $select->where("dep.product_id IN (" . implode(',', $productIds) . ")");
        }

        $connection->query($select->deleteFromSelect('dep'));
    }
}
