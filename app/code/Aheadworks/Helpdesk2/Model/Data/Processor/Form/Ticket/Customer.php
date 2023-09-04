<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket;

use Magento\Customer\Model\Group as CustomerGroup;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Directory\Model\Country;
use Magento\Directory\Model\CountryFactory;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\UrlBuilder;
use Magento\Customer\Api\GroupRepositoryInterface;

/**
 * Class Customer
 */
class Customer implements ProcessorInterface
{
    const IS_REGISTERED_CUSTOMER = 'is_registered_customer';
    const CUSTOMER = 'customer';
    const PROFILE_URL = 'backend_profile_url';
    const COUNTRY = 'country';
    const GROUP_CODE = 'group_code';

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param UrlBuilder $urlBuilder
     * @param CountryFactory $countryFactory
     * @param GroupRepositoryInterface $groupRepository
     */
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly DataObjectProcessor $dataObjectProcessor,
        private readonly UrlBuilder $urlBuilder,
        private readonly CountryFactory $countryFactory,
        private GroupRepositoryInterface $groupRepository
    ) {}

    /**
     * Prepare entity data for form
     *
     * @param array $data
     * @return array
     * @throws LocalizedException
     */
    public function prepareEntityData($data)
    {
        $data[self::IS_REGISTERED_CUSTOMER] = false;

        $customerEmail = $data[TicketInterface::CUSTOMER_EMAIL];
        $customerId = $data[TicketInterface::CUSTOMER_ID];

        $customer = $this->getCustomerByEmail($customerEmail);
        if (!$customer) {
            $customer = $this->getCustomerById($customerId);
        }

        $customerGroupId = CustomerGroup::NOT_LOGGED_IN_ID;
        if ($customer) {
            $data[self::CUSTOMER] = $this->dataObjectProcessor->buildOutputDataArray(
                $customer,
                CustomerInterface::class
            );
            $customerGroupId = $customer->getGroupId();
            $data[self::CUSTOMER][self::PROFILE_URL] =
                $this->urlBuilder->getBackendCustomerProfileLink($customer->getId());
            $data[self::CUSTOMER][self::COUNTRY] = $this->getCustomerCountry($customer);
            $data[self::IS_REGISTERED_CUSTOMER] = true;
        }

        $data[self::CUSTOMER][self::GROUP_CODE] = $this->groupRepository->getById($customerGroupId)->getCode();

        return $data;
    }

    /**
     * Retrieve customer by Email
     *
     * @param string $customerEmail
     * @return CustomerInterface|null
     * @throws LocalizedException
     */
    private function getCustomerByEmail($customerEmail)
    {
        try {
            return $this->customerRepository->get($customerEmail);
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }

    /**
     * Retrieve customer by Id
     *
     * @param string $customerEmail
     * @return CustomerInterface|null
     * @throws LocalizedException
     */
    private function getCustomerById($customerId)
    {
        try {
            return $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }

    /**
     * Get customer country
     *
     * @param CustomerInterface $customer
     * @return string
     */
    private function getCustomerCountry($customer)
    {
        foreach ($customer->getAddresses() as $address) {
            if ($address->isDefaultShipping()) {
                /** @var Country $country */
                $country = $this->countryFactory->create();
                return $country->loadByCode($address->getCountryId())->getName();
            }
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    public function prepareMetaData($meta)
    {
        return $meta;
    }
}
