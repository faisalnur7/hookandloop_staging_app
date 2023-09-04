<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Data;

use Magento\Framework\DataObject;
use Plumrocket\SocialLoginFree\Api\Data\NetworkAccountInterface;

/**
 * @since 3.2.0
 */
class Account extends DataObject implements NetworkAccountInterface
{
    /**
     * @inheritDoc
     */
    public function getNetworkCode(): string
    {
        return (string) $this->_getData('network_code');
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return (string) $this->_getData('user_id');
    }

    /**
     * @inheritDoc
     */
    public function getFirstName(): string
    {
        return (string) $this->_getData('firstname');
    }

    /**
     * @inheritDoc
     */
    public function getLastName(): string
    {
        return (string) $this->_getData('lastname');
    }

    /**
     * @inheritDoc
     */
    public function getEmail(): string
    {
        return (string) $this->_getData('email');
    }

    /**
     * @inheritDoc
     */
    public function getPhotoUrl(): string
    {
        return (string) $this->_getData('photo');
    }

    /**
     * @inheritDoc
     */
    public function getCustomerData(): array
    {
        $customerData = $this->getData();
        unset(
            $customerData['network_code'],
            $customerData['user_id'],
            $customerData['photo'],
            $customerData['additional']
        );
        return $customerData;
    }

    /**
     * Get additional data.
     *
     * Used for storing network user profile url and so on.
     *
     * @return array
     */
    public function getAdditionalData(): array
    {
        return $this->_getData('additional') ?: [];
    }

    /**
     * @inheritDoc
     */
    public function setAdditionalData(array $additionalData): NetworkAccountInterface
    {
        return $this->setData('additional', $additionalData);
    }

    /**
     * @inheritDoc
     */
    public function addAdditionalData(string $key, $value): NetworkAccountInterface
    {
        $data = $this->getAdditionalData();
        $data[$key] = $value;
        return $this->setAdditionalData($data);
    }

    /**
     * @inheritdoc
     */
    public function toArray(array $keys = []): array
    {
        if (empty($keys)) {
            return $this->getData();
        }

        return parent::toArray();
    }
}
