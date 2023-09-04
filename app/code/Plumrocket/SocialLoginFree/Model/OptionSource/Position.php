<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\OptionSource;

use Plumrocket\Base\Model\OptionSource\AbstractSource;

/**
 * @since 4.0.0
 */
class Position extends AbstractSource
{
    public const BUTTONS_POSITION_LOGIN = 'login';
    public const BUTTONS_POSITION_REGISTER = 'register';
    public const BUTTONS_POSITION_CHECKOUT = 'checkout';

    /**
     * @inheritDoc
     */
    public function toOptionHash(): array
    {
        return [
            self::BUTTONS_POSITION_LOGIN => __('Login Form'),
            self::BUTTONS_POSITION_REGISTER => __('Registration Form'),
            self::BUTTONS_POSITION_CHECKOUT => __('Checkout Page'),
        ];
    }
}
