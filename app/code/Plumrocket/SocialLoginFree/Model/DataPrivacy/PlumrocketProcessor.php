<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\DataPrivacy;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Serialize\SerializerInterface;
use Plumrocket\DataPrivacyApi\Api\DataExportProcessorInterface;
use Plumrocket\DataPrivacyApi\Api\DataRemovalProcessorInterface;
use Plumrocket\SocialLoginFree\Api\AccountLinkRepositoryInterface;
use Plumrocket\SocialLoginFree\Model\Account\Photo;

/**
 * @since 4.0.0
 */
class PlumrocketProcessor implements DataExportProcessorInterface, DataRemovalProcessorInterface
{
    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Photo
     */
    private $photoHelper;

    /**
     * @var \Plumrocket\SocialLoginFree\Api\AccountLinkRepositoryInterface
     */
    private $accountLinkRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @param \Plumrocket\SocialLoginFree\Model\Account\Photo                $photoHelper
     * @param \Plumrocket\SocialLoginFree\Api\AccountLinkRepositoryInterface $accountLinkRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder                   $searchCriteriaBuilder
     * @param \Magento\Framework\Serialize\SerializerInterface               $serializer
     */
    public function __construct(
        Photo $photoHelper,
        AccountLinkRepositoryInterface $accountLinkRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SerializerInterface $serializer
    ) {
        $this->photoHelper = $photoHelper;
        $this->accountLinkRepository = $accountLinkRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function exportCustomerData(CustomerInterface $customer): ?array
    {
        $returnData = [
            [
                'Network',
                'User Id',
                'Account Photo Url',
                'Additional Data',
            ],
        ];

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('customer_id', $customer->getId())
            ->create();
        $accountLinkCollection = $this->accountLinkRepository->getList($searchCriteria);

        if (! $accountLinkCollection->getTotalCount()) {
            return null;
        }

        foreach ($accountLinkCollection->getItems() as $accountLink) {
            $returnData[] = [
                $accountLink->getNetworkCode(),
                $accountLink->getNetworkUserId(),
                $this->photoHelper->getPhotoUrl($customer->getid()),
                $this->serializer->serialize($accountLink->getAdditionalData()),
            ];
        }

        return $returnData;
    }

    /**
     * @inheritDoc
     */
    public function exportGuestData(string $email): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getFileName(string $dateTime): string
    {
        return "Social_Login_Account_Data_$dateTime";
    }

    /**
     * @inheritDoc
     */
    public function deleteGuestData(string $email): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function deleteCustomerData(CustomerInterface $customer): bool
    {
        return $this->photoHelper->remove((int) $customer->getId());
    }
}
