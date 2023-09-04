<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Customer;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Store\Model\StoreManager;

/**
 * @since 4.0.0
 */
class GetCustomerIdByEmail
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Store\Model\StoreManager                                $storeManager
     */
    public function __construct(
        CustomerCollectionFactory $customerCollectionFactory,
        StoreManager $storeManager
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Get customer id by email.
     *
     * @param string $email
     * @param bool $useGlobalScope
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(string $email, bool $useGlobalScope = false): int
    {
        $collection = $this->customerCollectionFactory->create()
            ->addFieldToFilter('email', $email)
            ->setPageSize(1);

        if ($useGlobalScope === false) {
            $collection->addFieldToFilter(
                'website_id',
                $this->storeManager->getWebsite()->getId()
            );
        }

        return (int) $collection->getFirstItem()->getId();
    }
}
