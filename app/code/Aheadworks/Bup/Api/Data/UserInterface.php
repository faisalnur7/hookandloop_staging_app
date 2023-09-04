<?php
namespace Aheadworks\Bup\Api\Data;

use Magento\User\Api\Data\UserInterface as MagentoUserInterface;

/**
 * Interface UserInterface
 *
 * @package Aheadworks\Bup\Api\Data
 */
interface UserInterface extends MagentoUserInterface
{
    /**
     * Profile for magento backend user
     */
    const AW_BUP_USER_PROFILE = 'aw_bup_user_profile';
}
