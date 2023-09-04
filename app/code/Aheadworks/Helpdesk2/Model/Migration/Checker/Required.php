<?php
namespace Aheadworks\Helpdesk2\Model\Migration\Checker;

use Aheadworks\Helpdesk2\Model\Migration\Source\Helpdesk1TableNames;
use Aheadworks\Helpdesk2\Model\ThirdPartyModule\ModuleChecker;
use Magento\Framework\App\ResourceConnection;

/**
 * Class Required
 *
 * @package Aheadworks\Helpdesk2\Model\Migration\Checker
 */
class Required
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var ModuleChecker
     */
    private $moduleChecker;

    /**
     * @var string[]
     */
    private $tablesForCheck = [
        Helpdesk1TableNames::TICKET,
        Helpdesk1TableNames::TICKET_MESSAGE,
        Helpdesk1TableNames::DEPARTMENT,
        Helpdesk1TableNames::DEPARTMENT_LABEL,
        Helpdesk1TableNames::DEPARTMENT_PERMISSION,
        Helpdesk1TableNames::DEPARTMENT_GATEWAY,
        Helpdesk1TableNames::DEPARTMENT_GATEWAY_AUTH,
        Helpdesk1TableNames::QUICK_RESPONSE,
        Helpdesk1TableNames::QUICK_RESPONSE_TEXT,
        Helpdesk1TableNames::ATTACHMENT,
    ];

    /**
     * @param ResourceConnection $resourceConnection
     * @param ModuleChecker $moduleChecker
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        ModuleChecker $moduleChecker
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->moduleChecker = $moduleChecker;
    }

    /**
     * Check if migration from previous version required
     *
     * @return bool
     */
    public function isMigrationRequired()
    {
        if ($this->moduleChecker->isAwHelpDesk1Enabled()) {
            return true;
        }

        $connection = $this->resourceConnection->getConnection();
        foreach ($this->tablesForCheck as $tableName) {
            if ($connection->isTableExists($connection->getTableName($tableName))) {
                return true;
            }
        }

        return false;
    }
}
