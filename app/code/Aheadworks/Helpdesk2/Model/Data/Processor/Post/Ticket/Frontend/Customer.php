<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket\Frontend;

use Aheadworks\Helpdesk2\Model\Ticket\CustomerInfo;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;

/**
 * Class Customer
 */
class Customer implements ProcessorInterface
{
    /**
     * @param CustomerSession $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param Escaper $escaper
     * @param CustomerInfo $customerInfo
     */
    public function __construct(
        private readonly CustomerSession $customerSession,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly Escaper $escaper,
        private readonly CustomerInfo $customerInfo
    ) {}

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function prepareEntityData($data)
    {
        $customerId = $this->customerSession->getCustomerId();
        if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);
            $data[TicketInterface::CUSTOMER_ID] = $customer->getId();
            $data[TicketInterface::CUSTOMER_NAME] =
                $data[TicketInterface::CUSTOMER_NAME] ?? $customer->getFirstname() . ' ' . $customer->getLastname();
            $data[TicketInterface::CUSTOMER_EMAIL] = $data[TicketInterface::CUSTOMER_EMAIL] ?? $customer->getEmail();
            empty($data[TicketInterface::TELEPHONE]) ? $data[TicketInterface::TELEPHONE] = $this->customerInfo->getCustomerTelephone($customer)
                : $data[TicketInterface::TELEPHONE] = $this->escaper->escapeHtml($data[TicketInterface::TELEPHONE]);
        }

        return $data;
    }
}
