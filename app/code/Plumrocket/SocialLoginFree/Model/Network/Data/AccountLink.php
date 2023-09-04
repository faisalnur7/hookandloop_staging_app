<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Data;

use Magento\Framework\Model\AbstractModel;
use Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface;

/**
 * Data object for plumrocket_sociallogin_account table entities.
 *
 * @since 3.9.0
 */
class AccountLink extends AbstractModel implements NetworkAccountLinkInterface
{
    public const PHOTO_FILE_EXT = 'png';

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Network
     */
    private $networkConfig;

    /**
     * @param \Magento\Framework\Model\Context                                      $context
     * @param \Magento\Framework\Registry                                           $registry
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Network                     $networkConfig
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null          $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null                    $resourceCollection
     * @param array                                                                 $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Plumrocket\SocialLoginFree\Helper\Config\Network $networkConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->networkConfig = $networkConfig;
    }

    /**
     * Set resource model.
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(\Plumrocket\SocialLoginFree\Model\ResourceModel\AccountLink::class);
    }

    /**
     * @inheritDoc
     */
    public function getNetworkCode(): string
    {
        return (string) $this->_getData('type');
    }

    /**
     * @inheritDoc
     */
    public function getNetworkUserId(): string
    {
        return (string) $this->_getData('user_id');
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId(): int
    {
        return (int) $this->_getData('customer_id');
    }

    /**
     * @inheritDoc
     */
    public function getAdditionalData(): array
    {
        return (array) $this->_getData('additional');
    }

    /**
     * @inheritDoc
     */
    public function setNetworkCode(string $networkCode): NetworkAccountLinkInterface
    {
        return $this->setData('type', $networkCode);
    }

    /**
     * @inheritDoc
     */
    public function setNetworkUserId(string $networkId): NetworkAccountLinkInterface
    {
        return $this->setData('user_id', $networkId);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId(int $customerId): NetworkAccountLinkInterface
    {
        return $this->setData('customer_id', $customerId);
    }

    /**
     * @inheritDoc
     */
    public function setAdditionalData(array $additionalData): NetworkAccountLinkInterface
    {
        return $this->setData('additional', $additionalData);
    }

    /**
     * @inheritDoc
     */
    public function addAdditionalData(string $key, $value): NetworkAccountLinkInterface
    {
        $data = $this->getAdditionalData();
        $data[$key] = $value;
        return $this->setAdditionalData($data);
    }

    /**
     * @inheritDoc
     */
    public function getNetworkTitle(): string
    {
        return $this->networkConfig->getTitle($this->getNetworkCode());
    }
}
