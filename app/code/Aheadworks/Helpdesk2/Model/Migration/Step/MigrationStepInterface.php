<?php
namespace Aheadworks\Helpdesk2\Model\Migration\Step;

/**
 * Interface MigrationStepInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Migration\Step
 */
interface MigrationStepInterface
{
    /**
     * Migrate data
     *
     * @param int|null $limit
     * @return string
     * @throws \Exception
     */
    public function migrate($limit);
}
