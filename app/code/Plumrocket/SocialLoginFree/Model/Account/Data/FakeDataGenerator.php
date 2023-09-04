<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Account\Data;

use Plumrocket\SocialLoginFree\Helper\Config;

/**
 * @since 4.0.0
 */
class FakeDataGenerator
{
    public const GENDER_NOT_SPECIFIED = 3;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail
     */
    private $fakeEmail;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @param \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail $fakeEmail
     * @param \Plumrocket\SocialLoginFree\Helper\Config                $config
     */
    public function __construct(
        FakeEmail $fakeEmail,
        Config $config
    ) {
        $this->fakeEmail = $fakeEmail;
        $this->config = $config;
    }

    /**
     * Generate fake email if real is miss and admin allowed it.
     *
     * @param array $data
     * @return array
     */
    public function email(array $data): array
    {
        if (empty($data['email']) && $this->config->createFakeData()) {
            $data['email'] = $this->fakeEmail->generate();
        }
        return $data;
    }

    /**
     * Try parse names form email (only if it is real) if network didn't send them
     *
     * @param array $data
     * @return array
     */
    public function names(array $data): array
    {
        if (! empty($data['firstname']) && ! empty($data['lastname'])) {
            return $data;
        }

        if ($data['email'] && ! $this->fakeEmail->detect($data['email'])) {
            $login = $this->getLogin($data['email']);
            $loginParts = $this->splitLogin($login);

            if ((count($loginParts) === 1) && empty($data['lastname'])) {
                $data['lastname'] = ucfirst($loginParts[0]);
            }

            if (count($loginParts) === 2) {
                $data = $this->setMissedNames($data, $loginParts);
            }

            if (count($loginParts) >=3) {
                $data = $this->setMissedNamesFromLongLogin($data, $login);
            }
        }

        return $this->generateMissedNames($data);
    }

    /**
     * @param string $login
     * @return array
     */
    private function splitLogin(string $login): array
    {
        return preg_split('#[.\-]+#u', $login, 3);
    }

    /**
     * @param string $email
     * @return string
     */
    private function getLogin(string $email): string
    {
        return trim(strstr($email, '@', true));
    }

    /**
     * @param $data
     * @param $nameFromEmail
     * @return array
     */
    private function setMissedNamesFromLongLogin($data, $nameFromEmail): array
    {
        $name = preg_split('#[.]+#u', $nameFromEmail, 2);
        if (isset($name[1]) && strpos($name[1], '.') !== false) {
            $name[1] = strstr($name[1], '.', true);
        }
        if (count($name) === 1) {
            $name = preg_split('#[-]+#u', $nameFromEmail, 2);
        }

        if (empty($data['firstname'])) {
            $data['firstname'] = ucfirst($name[0]);
        }

        if (empty($data['lastname'])) {
            $data['lastname'] = ucfirst($name[1]);
        }

        return $data;
    }

    /**
     * @param $data
     * @param $loginParts
     * @return array
     */
    private function setMissedNames(array $data, array $loginParts): array
    {
        if (empty($data['firstname'])) {
            $data['firstname'] = ucfirst($loginParts[0]);
        }

        if (empty($data['lastname'])) {
            $data['lastname'] = ucfirst($loginParts[1]);
        }

        return $data;
    }

    /**
     * @param $data
     * @return array
     */
    private function generateMissedNames(array $data): array
    {
        if (empty($data['firstname'])) {
            $data['firstname'] = 'Customer';
        }
        if (empty($data['lastname'])) {
            $data['lastname'] = 'Unknown';
        }

        return $data;
    }

    /**
     * Fix format of date and set default if needed
     *
     * @param array $data
     * @return array
     */
    public function dateOfBirth(array $data): array
    {
        if (empty($data['dob']) && $this->config->createFakeData()) {
            $data['dob'] = '1901-01-01';
        }
        return $data;
    }

    /**
     * Convert network gender types/labels to magento ids
     *
     * @param array $data
     * @return array
     */
    public function gender(array $data): array
    {
        if (0 !== $data['gender'] && 1 !== $data['gender']) {
            $data['gender'] = self::GENDER_NOT_SPECIFIED;
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function taxVat(array $data): array
    {
        if (! $data['taxvat'] && $this->config->createFakeData()) {
            $data['taxvat'] = '0';
        }
        return $data;
    }
}
