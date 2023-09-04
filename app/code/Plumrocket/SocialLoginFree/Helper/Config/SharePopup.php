<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Helper\Config;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Plumrocket\Base\Api\ConfigUtilsInterface;

/**
 * @since 4.0.0
 */
class SharePopup extends AbstractHelper
{

    public const XML_PATH_ENABLED = 'psloginfree/share/enable';
    public const XML_PATH_TITLE = 'psloginfree/share/title';
    public const XML_PATH_DESCRIPTION = 'psloginfree/share/description';
    public const XML_PATH_PAGE = 'psloginfree/share/page';
    public const XML_PATH_PAGE_LINK = 'psloginfree/share/page_link';

    /**
     * @var \Plumrocket\Base\Api\ConfigUtilsInterface
     */
    private $configUtils;

    /**
     * @param \Magento\Framework\App\Helper\Context     $context
     * @param \Plumrocket\Base\Api\ConfigUtilsInterface $configUtils
     */
    public function __construct(Context $context, ConfigUtilsInterface $configUtils)
    {
        parent::__construct($context);
        $this->configUtils = $configUtils;
    }

    /**
     * Check if share popup is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->configUtils->isSetFlag(self::XML_PATH_ENABLED);
    }

    /**
     * Retrieve popup title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return (string) $this->configUtils->getStoreConfig(self::XML_PATH_TITLE);
    }

    /**
     * Retrieve popup description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return (string) $this->configUtils->getStoreConfig(self::XML_PATH_DESCRIPTION);
    }

    /**
     * Retrieve page.
     *
     * @return string
     */
    public function getPage(): string
    {
        return (string) $this->configUtils->getStoreConfig(self::XML_PATH_PAGE);
    }

    /**
     * Retrieve custom page url.
     *
     * @return string
     */
    public function getCustomPageUrl(): string
    {
        return (string) $this->configUtils->getStoreConfig(self::XML_PATH_PAGE_LINK);
    }
}
