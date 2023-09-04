<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Data;

use Magento\Framework\DataObject;
use Plumrocket\SocialLoginFree\Api\Data\ButtonInterface;
use Plumrocket\SocialLoginFree\Api\NetworkButtonResolverInterface;

/**
 * @since 4.0.0
 */
class Button extends DataObject implements ButtonInterface
{
    public const NETWORK_CODE = 'type';
    public const POPUP_WIDTH = 'popup_width';
    public const POPUP_HEIGHT = 'popup_height';
    public const LOGIN_TEXT = 'login_text';
    public const REGISTER_TEXT = 'register_text';
    public const URL = 'href';
    public const LOGIN_URL = 'login_href';
    public const REGISTER_URL = 'register_href';
    public const DESIGN = 'design';
    public const OPEN_ON_PAGE = 'open_on_page';

    /**
     * @inheritdoc
     */
    public function setNetworkCode(string $networkCode): void
    {
        $this->setData(self::NETWORK_CODE, $networkCode);
    }

    /**
     * @inheritdoc
     */
    public function getNetworkCode(): string
    {
        return (string) $this->_getData(self::NETWORK_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setModalWidth(int $width): void
    {
        $this->setData(self::POPUP_WIDTH, $width);
    }

    /**
     * @inheritdoc
     */
    public function getModalWidth(): int
    {
        return (int) $this->_getData(self::POPUP_WIDTH);
    }

    /**
     * @inheritdoc
     */
    public function setModalHeight(int $height): void
    {
        $this->setData(self::POPUP_HEIGHT, $height);
    }

    /**
     * @inheritdoc
     */
    public function getModalHeight(): int
    {
        return (int) $this->_getData(self::POPUP_HEIGHT);
    }

    /**
     * @inheritdoc
     */
    public function setLoginText(string $text): void
    {
        $this->setData(self::LOGIN_TEXT, $text);
    }

    /**
     * @inheritdoc
     */
    public function getLoginText(): string
    {
        return (string) $this->_getData(self::LOGIN_TEXT);
    }

    /**
     * @inheritdoc
     */
    public function setRegisterText(string $text): void
    {
        $this->setData(self::REGISTER_TEXT, $text);
    }

    /**
     * @inheritdoc
     */
    public function getRegisterText(): string
    {
        return (string) $this->_getData(self::REGISTER_TEXT);
    }

    /**
     * @inheritdoc
     */
    public function setUrl(string $url): void
    {
        $this->setData(self::URL, $url);
    }

    /**
     * @inheritdoc
     */
    public function getUrl(): string
    {
        return (string) $this->_getData(self::URL);
    }

    /**
     * @inheritdoc
     */
    public function setLoginUrl(string $url): void
    {
        $this->setData(self::LOGIN_URL, $url);
    }

    /**
     * @inheritdoc
     */
    public function getLoginUrl(array $params = []): string
    {
        if (! $params && $this->_getData(self::LOGIN_URL)) {
            return (string) $this->_getData(self::LOGIN_URL);
        }
        return $this->getButtonResolver()->resolveLoginUrl($this->getNetworkCode(), $params);
    }

    /**
     * @inheritdoc
     */
    public function setRegisterUrl(string $url): void
    {
        $this->setData(self::REGISTER_URL, $url);
    }

    /**
     * @inheritdoc
     */
    public function getRegisterUrl(array $params = []): string
    {
        if (! $params && $this->_getData(self::REGISTER_URL)) {
            return (string) $this->_getData(self::REGISTER_URL);
        }
        return $this->getButtonResolver()->resolveRegistrationUrl($this->getNetworkCode(), $params);
    }

    /**
     * @inheritdoc
     */
    public function setDesign(string $design): void
    {
        $this->setData(self::DESIGN, $design);
    }

    /**
     * @inheritdoc
     */
    public function getDesign(): string
    {
        return (string) $this->_getData(self::DESIGN);
    }

    /**
     * Get button resolver that creates this button.
     *
     * @return \Plumrocket\SocialLoginFree\Api\NetworkButtonResolverInterface
     */
    public function getButtonResolver(): NetworkButtonResolverInterface
    {
        return $this->_getData('buttonResolver');
    }

    /**
     * Set button resolver while creating button.
     *
     * @param \Plumrocket\SocialLoginFree\Api\NetworkButtonResolverInterface $buttonResolver
     * @return void
     */
    public function setButtonResolver(NetworkButtonResolverInterface $buttonResolver): void
    {
        $this->setData('buttonResolver', $buttonResolver);
    }

    /**
     * @inheritdoc
     */
    public function toArray(array $keys = []): array
    {
        $data = parent::toArray($keys);
        unset($data['buttonResolver']);
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function shouldOpenOnPage(): bool
    {
        return (bool) $this->getData(self::OPEN_ON_PAGE);
    }

    /**
     * @inheritDoc
     */
    public function setOpenOnPage(bool $value)
    {
        $this->setData(self::OPEN_ON_PAGE, $value);
    }
}
