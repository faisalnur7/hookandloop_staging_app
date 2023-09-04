<?php
namespace Aheadworks\Helpdesk2\Setup\Patch\Data;

use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as TicketStatus;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message;

class AddCustomerAndAgentInMessageTable implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Set customer id and agent id in message table
     *
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     * @return $this
     */
    public function apply()
    {
        $messageTable = $this->moduleDataSetup->getTable(Message::MAIN_TABLE_NAME);
        $ticketTable = $this->moduleDataSetup->getTable(Ticket::TICKET_ENTITY_TABLE_NAME);
        $adminUserTable = $this->moduleDataSetup->getTable('admin_user');
            $this->moduleDataSetup->getConnection()->query(
            'UPDATE ' . $messageTable . '
                LEFT JOIN ' . $ticketTable . ' ON '.$ticketTable.'.entity_id = '.$messageTable.'.ticket_id
                LEFT JOIN '.$adminUserTable.' ON
                CONCAT('
                .$adminUserTable.'.firstname, " ", '.$adminUserTable.'.lastname
                ) = '.$messageTable.'.author_name AND '.$messageTable.'.type = "admin-message"'
                . ' SET '
                . $messageTable . '.agent_id = ' . $adminUserTable . '.user_id
                where ' . $messageTable . '.type in ("' . Type::ADMIN . '")'
        );
        $this->moduleDataSetup->getConnection()->query('UPDATE ' . $messageTable . '
             SET ' . $messageTable . '.status_id = IF(
                     ' . $messageTable . '.content like "%<b>Closed</b>%",
                     ' . TicketStatus::CLOSED . ',
                     IF(' . $messageTable . '.content like "%<b>New</b>%",
                        ' . TicketStatus::NEW . ',
                        IF(' . $messageTable . '.content like "%<b>Open</b>%",
                           ' . TicketStatus::OPEN . ',
                           IF(' . $messageTable . '.content like "%<b>Waiting for a customer</b>%",
                              ' . TicketStatus::WAITING . ',
                                null
                               )
                            )
                         )
                 )
             where ' . $messageTable . '.type in ("' . Type::SYSTEM . '")'
        );
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }
}
