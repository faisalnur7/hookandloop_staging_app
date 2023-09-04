<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Setup\Patch\Data;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Setup\TicketEavSetup;
use Aheadworks\Helpdesk2\Setup\TicketEavSetupFactory;
use Aheadworks\Helpdesk2\Model\Ticket as TicketModel;

class AddTelephoneAttribute implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param TicketEavSetupFactory $ticketSetupFactory
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly TicketEavSetupFactory $ticketSetupFactory
    ) {}

    /**
     * Install telephone attribute
     *
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        /** @var TicketEavSetup $ticketSetup */
        $ticketSetup = $this->ticketSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $ticketSetup->addAttribute(
            TicketModel::ENTITY,
            TicketInterface::TELEPHONE,
            ['type' => 'static']
        );

        return $this;
    }

    /**
     * Remove patch on uninstall command in order to be able to install it again
     */
    public function revert()
    {
        return true;
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
        return [
            InstallTicketAttributes::class
        ];
    }
}
