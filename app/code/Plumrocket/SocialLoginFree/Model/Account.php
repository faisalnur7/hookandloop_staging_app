<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model;

/**
 * @deprecated since 4.0.0
 * @see \Plumrocket\SocialLoginFree\Model\Network\Data\AccountLink
 */
class Account extends \Plumrocket\SocialLoginFree\Model\Network\Data\AccountLink
{

    /**
     * @deprecated since 4.0.0
     * @see \Plumrocket\SocialLoginFree\Model\Account\Data\FakeDataGenerator::GENDER_NOT_SPECIFIED
     * @var int
     */
    const GENDER_NOT_SPECIFIED = 3;
}
