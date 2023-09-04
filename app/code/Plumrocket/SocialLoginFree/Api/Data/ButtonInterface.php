<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Api\Data;

/**
 * @since 4.0.0
 */
interface ButtonInterface
{
    public const CUSTOMER_ACTION_LOGIN = 'login';
    public const CUSTOMER_ACTION_REGISTER = 'register';

    /**
     * Set network code.
     *
     * @param string $networkCode
     * @return void
     */
    public function setNetworkCode(string $networkCode): void;

    /**
     * Get network code.
     *
     * @return string
     */
    public function getNetworkCode(): string;

    /**
     * Set login modal width.
     *
     * @param int $width
     * @return void
     */
    public function setModalWidth(int $width): void;

    /**
     * Get login modal width.
     *
     * @return int
     */
    public function getModalWidth(): int;

    /**
     * Set login modal height.
     *
     * @param int $height
     * @return void
     */
    public function setModalHeight(int $height): void;

    /**
     * Get login modal height.
     *
     * @return int
     */
    public function getModalHeight(): int;

    /**
     * Set button text for login form.
     *
     * @param string $text
     * @return void
     */
    public function setLoginText(string $text): void;

    /**
     * Get button text for login form.
     *
     * @return string
     */
    public function getLoginText(): string;

    /**
     * Set button text for register form.
     *
     * @param string $text
     * @return void
     */
    public function setRegisterText(string $text): void;

    /**
     * Get button text for register form.
     *
     * @return string
     */
    public function getRegisterText(): string;

    /**
     * Set button url.
     *
     * @param string $url
     * @return void
     */
    public function setUrl(string $url): void;

    /**
     * Get button url.
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Set button url for login form.
     *
     * @param string $url
     * @return void
     */
    public function setLoginUrl(string $url): void;

    /**
     * Get button url for login form.
     *
     * @param array $params
     * @return string
     */
    public function getLoginUrl(array $params = []): string;

    /**
     * Set button url for register form.
     *
     * @param string $url
     * @return void
     */
    public function setRegisterUrl(string $url): void;

    /**
     * Get button url for register form.
     *
     * @param array $params
     * @return string
     */
    public function getRegisterUrl(array $params = []): string;

    /**
     * Set button design.
     *
     * @param string $design
     * @return void
     */
    public function setDesign(string $design): void;

    /**
     * Get button design.
     *
     * @return string
     */
    public function getDesign(): string;

    /**
     * Get network button config
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Check if it should be open on the page.
     *
     * @return bool
     */
    public function shouldOpenOnPage(): bool;

    /**
     * Set shop open on page.
     *
     * @param bool $value
     */
    public function setOpenOnPage(bool $value);
}
