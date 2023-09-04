<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Plumrocket\SocialLoginFree\Api\AccountLinkRepositoryInterface;
use Plumrocket\SocialLoginFree\Api\Data\AccountLinkResultsInterfaceFactory;
use Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface;
use Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterfaceFactory;
use Plumrocket\SocialLoginFree\Model\ResourceModel\AccountLink;
use Plumrocket\SocialLoginFree\Model\ResourceModel\AccountLink\CollectionFactory;

/**
 * @since 4.0.0
 */
class AccountLinkRepository implements AccountLinkRepositoryInterface
{
    /**
     * @var AccountLink
     */
    private $accountLinkResource;

    /**
     * @var \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterfaceFactory
     */
    private $accountLinkInterfaceFactory;

    /**
     * @var array
     */
    private $instancesById = [];

    /**
     * @var \Plumrocket\SocialLoginFree\Model\ResourceModel\AccountLink\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var \Plumrocket\SocialLoginFree\Api\Data\AccountLinkResultsInterfaceFactory
     */
    private $accountLinkResultsFactory;

    /**
     * @param \Plumrocket\SocialLoginFree\Model\ResourceModel\AccountLink                   $accountLinkResource
     * @param \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterfaceFactory       $accountLinkInterfaceFactory
     * @param \Plumrocket\SocialLoginFree\Model\ResourceModel\AccountLink\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface            $collectionProcessor
     * @param \Plumrocket\SocialLoginFree\Api\Data\AccountLinkResultsInterfaceFactory       $accountLinkResultsFactory
     */
    public function __construct(
        AccountLink $accountLinkResource,
        NetworkAccountLinkInterfaceFactory $accountLinkInterfaceFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        AccountLinkResultsInterfaceFactory $accountLinkResultsFactory
    ) {
        $this->accountLinkResource = $accountLinkResource;
        $this->accountLinkInterfaceFactory = $accountLinkInterfaceFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->accountLinkResultsFactory = $accountLinkResultsFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(NetworkAccountLinkInterface $accountLink): NetworkAccountLinkInterface
    {
        $this->accountLinkResource->save($accountLink);
        return $this->getById((int) $accountLink->getId(), true);
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var \Plumrocket\SocialLoginFree\Api\Data\AccountLinkResultsInterface $searchResult */
        $searchResult = $this->accountLinkResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $accountLinkId, bool $forceLoad = false): NetworkAccountLinkInterface
    {
        if (! isset($this->instancesById[$accountLinkId]) || $forceLoad) {
            $accountLinkCollection = $this->accountLinkInterfaceFactory->create();
            $this->accountLinkResource->load($accountLinkCollection, $accountLinkId);
            if (! $accountLinkCollection->getId()) {
                throw NoSuchEntityException::singleField(AccountLink::ID_FIELD_NAME, $accountLinkId);
            }
            $this->instancesById[$accountLinkCollection->getId()] = $accountLinkCollection;
        }

        return $this->instancesById[$accountLinkId];
    }

    /**
     * @inheritDoc
     */
    public function delete(NetworkAccountLinkInterface $accountLink): bool
    {
        $accountLinkId = $accountLink->getId();
        try {
            unset($this->instancesById[$accountLinkId]);
            $this->accountLinkResource->delete($accountLink);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('The "%1" link couldn\'t be removed.', $accountLinkId)
            );
        }
        unset($this->instancesById[$accountLinkId]);
        return true;
    }
}
