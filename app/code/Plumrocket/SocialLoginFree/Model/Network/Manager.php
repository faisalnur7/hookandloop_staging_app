<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Network;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Validation\ValidationException;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\SocialLoginFree\Api\AccountLinkRepositoryInterface;
use Plumrocket\SocialLoginFree\Api\CustomerNetworksManagerInterface;
use Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface;
use Psr\Log\LoggerInterface;

class Manager implements CustomerNetworksManagerInterface
{

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var array[]
     */
    private $typesCache = [];

    /**
     * @var array[]
     */
    private $accountLinksCache = [];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Plumrocket\Base\Model\Utils\Config
     */
    private $config;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Photo
     */
    private $photo;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterfaceFactory
     */
    private $accountLinkFactory;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\ResourceModel\AccountLink
     */
    private $accountLinkResource;

    /**
     * @var \Plumrocket\SocialLoginFree\Api\AccountLinkRepositoryInterface
     */
    private $accountLinkRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $timezone;

    /**
     * @param \Magento\Customer\Helper\Session\CurrentCustomer                        $currentCustomer
     * @param \Magento\Store\Model\StoreManagerInterface                              $storeManager
     * @param \Plumrocket\Base\Model\Utils\Config                                     $config
     * @param \Plumrocket\SocialLoginFree\Helper\Config                               $configHelper
     * @param \Plumrocket\SocialLoginFree\Model\Account\Photo                         $photo
     * @param \Psr\Log\LoggerInterface                                                $logger
     * @param \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterfaceFactory $accountLinkFactory
     * @param \Plumrocket\SocialLoginFree\Model\ResourceModel\AccountLink             $accountLinkResource
     * @param \Plumrocket\SocialLoginFree\Api\AccountLinkRepositoryInterface          $accountLinkRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder                            $searchCriteriaBuilder
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface                    $timezone
     */
    public function __construct(
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        StoreManagerInterface $storeManager,
        \Plumrocket\Base\Model\Utils\Config $config,
        \Plumrocket\SocialLoginFree\Helper\Config $configHelper,
        \Plumrocket\SocialLoginFree\Model\Account\Photo $photo,
        LoggerInterface $logger,
        \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterfaceFactory $accountLinkFactory,
        \Plumrocket\SocialLoginFree\Model\ResourceModel\AccountLink $accountLinkResource,
        AccountLinkRepositoryInterface $accountLinkRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TimezoneInterface $timezone
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->configHelper = $configHelper;
        $this->photo = $photo;
        $this->logger = $logger;
        $this->accountLinkFactory = $accountLinkFactory;
        $this->accountLinkResource = $accountLinkResource;
        $this->accountLinkRepository = $accountLinkRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->timezone = $timezone;
    }

    /**
     * @inheritDoc
     */
    public function getLinkedTypesByCustomerId(int $customerId): array
    {
        if (! array_key_exists($customerId, $this->typesCache)) {
            $networks = $this->getLinkedNetworksByCustomerId($customerId);

            $this->typesCache[$customerId] = array_map(
                static function (NetworkAccountLinkInterface $accountLink) {
                    return $accountLink->getNetworkCode();
                },
                $networks
            );
        }

        return $this->typesCache[$customerId];
    }

    /**
     * @inheritDoc
     */
    public function getLinkedNetworksByCustomerId(int $customerId): array
    {
        if (! $customerId) {
            return [];
        }

        if (! array_key_exists($customerId, $this->accountLinksCache)) {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('customer_id', $customerId)
                ->create();
            $searchResult = $this->accountLinkRepository->getList($searchCriteria);

            $accountLinks = [];
            foreach ($searchResult->getItems() as $networkAccountLink) {
                $accountLinks[] = $networkAccountLink;
            }

            $this->accountLinksCache[$customerId] = $accountLinks;
        }

        return $this->accountLinksCache[$customerId];
    }

    /**
     * @inheritDoc
     */
    public function getLinkedTypesForCurrentCustomer(): array
    {
        return $this->getLinkedTypesByCustomerId((int) $this->currentCustomer->getCustomerId());
    }

    /**
     * @inheritDoc
     */
    public function getLinkedNetworksForCurrentCustomer(): array
    {
        return $this->getLinkedNetworksByCustomerId((int) $this->currentCustomer->getCustomerId());
    }

    /**
     * @inheritDoc
     */
    public function linkNetworkToCustomer(
        string $type,
        string $userId,
        int $customerId,
        ?string $customerPhoto = null,
        ?array $additionalData = null
    ): void {
        if (! $customerId || ! $userId || ! $type) {
            throw new ValidatorException(
                __('One of the params "customerId", "userId", "type" is empty')
            );
        }

        if ($linkedCustomerId = $this->getCustomerIdByNetwork($type, $userId)) {
            if ($linkedCustomerId === $customerId) {
                throw new AlreadyExistsException(
                    __('This %1 account has already been assigned to your store account.', $type)
                );
            }

            throw new AlreadyExistsException(
                __('This %1 account has already been assigned to another store account.', $type)
            );
        }

        /** @var \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface $accountLink */
        $accountLink = $this->accountLinkFactory->create();

        $accountLink->setNetworkCode($type);
        $accountLink->setNetworkUserId($userId);
        $accountLink->setCustomerId($customerId);
        $accountLink->setAdditionalData($additionalData ?: []);
        $data = [
            'created_at' => $this->getUtcDateTime(),
        ];
        $accountLink->addData($data);

        $this->accountLinkRepository->save($accountLink);

        if ($customerPhoto && $this->configHelper->isPhotoEnabled()) {
            try {
                $this->photo->saveExternal($customerId, $customerPhoto);
            } catch (ValidationException $e) {
                $this->logger->debug($e->getMessage());
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function unlinkNetworkFromCustomer(int $customerId, int $accountLinkId): bool
    {
        try {
            $accountLink = $this->accountLinkRepository->getById($accountLinkId);
            if ($accountLink->getCustomerId() !== $customerId) {
                return false;
            }
            return $this->accountLinkRepository->delete($accountLink);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function isNetworkAlreadyLinked(string $networkCode, string $userId): bool
    {
        return (bool) $this->getCustomerIdByNetwork($networkCode, $userId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerIdByNetwork(string $networkCode, string $userId): int
    {
        // Separate customer for each website.
        if (1 === (int) $this->config->getConfig('customer/account_share/scope')) {
            return $this->accountLinkResource->getCustomerIdByNetworkId(
                $networkCode,
                $userId,
                (int) $this->storeManager->getWebsite()->getId()
            );
        }
        return $this->accountLinkResource->getCustomerIdByNetworkId($networkCode, $userId);
    }

    /**
     * Get utc date time.
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getUtcDateTime(): string
    {
        return $this->timezone->convertConfigTimeToUtc($this->timezone->date()->format('Y-m-d H:i:s'));
    }
}
