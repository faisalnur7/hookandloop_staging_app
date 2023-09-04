<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Integration;

use Plumrocket\SocialLoginFree\Model\Network\Modal\DefaultUrlResolver;

/**
 * @since 4.1.0
 */
class FacebookUrlResolver extends DefaultUrlResolver
{

    /**
     * Customize scope.
     *
     * @return array
     */
    public function getUrlParams(): array
    {
        return $this->addBirthdayScope(parent::getUrlParams());
    }

    /**
     * Add birthday to the scope.
     *
     * @param array $urlParams
     * @return array
     */
    private function addBirthdayScope(array $urlParams): array
    {
        $askBirthday = (bool) $this->networkConfig->getNetworkConfig('facebook', 'enable_birthday');
        if (! $askBirthday) {
            return $urlParams;
        }

        $scope = explode(',', (string) ($urlParams['scope'] ?? ''));
        if (! in_array('user_birthday', $scope, true)) {
            $scope[] = 'user_birthday';
            $urlParams['scope'] = implode(',', $scope);
        }
        return $urlParams;
    }
}
