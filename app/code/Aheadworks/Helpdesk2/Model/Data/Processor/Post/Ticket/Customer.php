<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket;

use Aheadworks\Helpdesk2\Model\Ticket\CustomerInfo;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;

/**
 * Class Customer
 */
class Customer implements ProcessorInterface
{
    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param Escaper $escaper
     * @param CustomerInfo $customerInfo
     */
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly Escaper $escaper,
        private readonly CustomerInfo $customerInfo
    ) {}

    /**
     * Prepare entity data for save
     *
     * @param array $data
     * @return array
     * @throws LocalizedException
     */
    public function prepareEntityData($data)
    {
        if (isset($data[TicketInterface::CUSTOMER_EMAIL]) && empty($data[TicketInterface::CUSTOMER_ID])) {
            try {
                $customer = $this->customerRepository->get($data[TicketInterface::CUSTOMER_EMAIL]);
                $data[TicketInterface::CUSTOMER_ID] = $customer->getId();
                empty($data[TicketInterface::TELEPHONE]) ? $data[TicketInterface::TELEPHONE] = $this->customerInfo->getCustomerTelephone($customer)
                    : $data[TicketInterface::TELEPHONE] = $this->escaper->escapeHtml($data[TicketInterface::TELEPHONE]);
            } catch (NoSuchEntityException $exception) {
                $data[TicketInterface::CUSTOMER_ID] = null;
                empty($data[TicketInterface::TELEPHONE]) ? $data[TicketInterface::TELEPHONE] = null
                    : $data[TicketInterface::TELEPHONE] = $this->escaper->escapeHtml($data[TicketInterface::TELEPHONE]);
            }
        }

        return $data;
    }
}
