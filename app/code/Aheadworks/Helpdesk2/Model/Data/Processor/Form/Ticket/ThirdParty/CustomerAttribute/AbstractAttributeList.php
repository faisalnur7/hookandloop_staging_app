<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket\ThirdParty\CustomerAttribute;

use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\ThirdPartyModule\Aheadworks\CustomerAttributes\CustomerLoader;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket\ThirdParty\CustomerAttribute\ComponentDataResolver;

/**
 * Class AbstractAttributeList
 */
abstract class AbstractAttributeList implements ProcessorInterface
{
    /**
     * AbstractAttributeList constructor.
     *
     * @param TicketRepositoryInterface $ticketRepository
     * @param CustomerLoader $customerLoader
     * @param ComponentDataResolver $componentDataResolver
     */
    public function __construct(
        private TicketRepositoryInterface $ticketRepository,
        private CustomerLoader $customerLoader,
        private ComponentDataResolver $componentDataResolver
    ) {
    }

    /**
     * Prepare entity data for form
     *
     * @param array $data
     * @return array
     */
    public function prepareEntityData($data): array
    {
        try {
            $ticket = $this->ticketRepository->getById($data['ticket_id']);
            $customer = $this->customerLoader->loadDataByCustomerEmail($ticket->getCustomerEmail());
            $data['customer'] = $customer->getData();
            $this->componentDataResolver->overrideCustomerAttributesData($customer, $data['customer']);

        } catch (\Exception $exception) {
            $data['customer'] = [];
        }

        return $data;
    }

    /**
     * Prepare meta data for form
     *
     * @param array $meta
     * @return array
     */
    abstract public function prepareMetaData($meta): array;
}
