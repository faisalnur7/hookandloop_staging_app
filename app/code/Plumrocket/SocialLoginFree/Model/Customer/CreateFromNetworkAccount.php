<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\CustomerFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Exception\InvalidArgumentException;
use Magento\Framework\Validator\EmailAddress as EmailValidator;
use Magento\Newsletter\Model\SubscriberFactory;
use Plumrocket\SocialLoginFree\Api\CreateCustomerFromNetworkAccountInterface;
use Plumrocket\SocialLoginFree\Api\Data\NetworkAccountInterface;
use Plumrocket\SocialLoginFree\Model\Account\Data\FakeDataGenerator;
use Plumrocket\SocialLoginFree\Model\Account\Data\PasswordGenerator;

/**
 * @since 3.8.0
 */
class CreateFromNetworkAccount implements CreateCustomerFromNetworkAccountInterface
{

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var \Magento\Customer\Model\Attribute
     */
    private $attribute;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Data\PasswordGenerator
     */
    private $passwordGenerator;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Data\FakeDataGenerator
     */
    private $fakeDataGenerator;

    /**
     * @var \Magento\Framework\Validator\EmailAddress
     */
    private $emailValidator;

    /**
     * @param \Magento\Customer\Model\CustomerFactory                          $customerFactory
     * @param \Magento\Eav\Model\Config                                        $eavConfig
     * @param \Magento\Customer\Model\Attribute                                $attribute
     * @param \Plumrocket\SocialLoginFree\Model\Account\Data\PasswordGenerator $passwordGenerator
     * @param \Magento\Customer\Api\CustomerRepositoryInterface                $customerRepository
     * @param \Plumrocket\SocialLoginFree\Model\Account\Data\FakeDataGenerator $fakeDataGenerator
     * @param \Magento\Framework\Validator\EmailAddress                        $emailValidator
     */
    public function __construct(
        CustomerFactory $customerFactory,
        EavConfig $eavConfig,
        Attribute $attribute,
        PasswordGenerator $passwordGenerator,
        CustomerRepositoryInterface $customerRepository,
        FakeDataGenerator $fakeDataGenerator,
        EmailValidator $emailValidator
    ) {
        $this->customerFactory = $customerFactory;
        $this->eavConfig = $eavConfig;
        $this->attribute = $attribute;
        $this->passwordGenerator = $passwordGenerator;
        $this->customerRepository = $customerRepository;
        $this->fakeDataGenerator = $fakeDataGenerator;
        $this->emailValidator = $emailValidator;
    }

    /**
     * @inheritDoc
     */
    public function execute(NetworkAccountInterface $networkAccount): CustomerInterface
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->customerFactory->create();

        $data = $this->fakeDataGenerator->email($networkAccount->getCustomerData());
        $data = $this->fakeDataGenerator->names($data);
        $data = $this->fakeDataGenerator->dateOfBirth($data);
        $data = $this->fakeDataGenerator->gender($data);
        $data = $this->fakeDataGenerator->taxVat($data);
        $password = $this->passwordGenerator->generatePassword();

        $customer->setData($data);
        $customer->setConfirmation($customer->getRandomConfirmationKey());
        $customer->setPassword($password);
        $customer->setPasswordConfirmation($password);
        $customer->setData('is_active', 1);
        $customer->getGroupId();

        $errors = $this->_validateErrors($customer);
        if (! empty($errors)) {
            throw new InvalidArgumentException(array_shift($errors));
        }

        if (! $this->emailValidator->isValid($customer->getEmail())) {
            throw new InvalidArgumentException(__('Email address incorrect.'));
        }

        $customerId = $customer->save()->getId();

        // Set email confirmation;
        $customer->setConfirmation(null)->save();

        return $this->customerRepository->getById($customerId);
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    protected function _validateErrors(\Magento\Customer\Model\Customer $customer)
    {
        $errors = [];

        // Date of birth.
        $entityType = $this->eavConfig->getEntityType('customer');
        $attribute = $this->attribute->loadByCode($entityType, 'dob');

        if ($attribute->getIsRequired()
            && $customer->getDob()
            && ! (new \Laminas\Validator\Date())->isValid($customer->getDob())
        ) {
            $errors[] = __('The Date of Birth is not correct.');
        }

        if (true !== ($customerErrors = $customer->validate())) {
            $errors = array_merge($customerErrors, $errors);
        }

        return $errors;
    }
}
