<?php
declare(strict_types=1);

namespace Aheadworks\Bup\Setup\Patch\Data;

use Aheadworks\Bup\Api\Data\UserInterface;
use Aheadworks\Bup\Api\Data\UserProfileInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddAdminUsers implements DataPatchInterface
{
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    /**
     * Apply patch
     *
     * @return void
     */
    public function apply(): void
    {
        $this->addAdminUsers($this->moduleDataSetup);
    }

    /**
     * Get aliases
     *
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Get dependencies
     *
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Add admin users in table aw_bup_user_profile
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function addAdminUsers(ModuleDataSetupInterface $setup): void
    {
        $adminUserSelect = clone $setup->getConnection()->select();

        $columns = [
            UserProfileInterface::USER_ID => 'admin_user.user_id'
        ];

        $userProfileTable = $setup->getTable(UserInterface::AW_BUP_USER_PROFILE);

        $adminUserSelect
            ->from(['admin_user' => $setup->getTable('admin_user')])
            ->where(UserProfileInterface::USER_ID. ' NOT IN (
                SELECT '. UserProfileInterface::USER_ID .' from '. $userProfileTable .')')
            ->reset('columns')
            ->columns($columns);

        $query = $adminUserSelect->insertFromSelect(
            $userProfileTable,
            array_keys($columns)
        );

        $setup->getConnection()->query($query);
    }
}
