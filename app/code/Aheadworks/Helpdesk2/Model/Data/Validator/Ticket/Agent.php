<?php
namespace Aheadworks\Helpdesk2\Model\Data\Validator\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Source\Department\AgentList;
use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Agent
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Validator\Ticket
 */
class Agent extends AbstractValidator
{
    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        DepartmentRepositoryInterface $departmentRepository,
        LoggerInterface $logger
    ) {
        $this->departmentRepository = $departmentRepository;
        $this->logger = $logger;
    }

    /**
     * Check if agent is correct
     *
     * @param TicketInterface $ticket
     * @return bool
     * @throws \Exception
     */
    public function isValid($ticket)
    {
        $this->_clearMessages();

        $department = $this->departmentRepository->get($ticket->getDepartmentId());
        $agentId = $ticket->getAgentId();
        $primaryAgentId = $department->getPrimaryAgentId();
        $departmentAgentIds = $department->getAgentIds();
        if ($agentId && $primaryAgentId != $agentId && !in_array($agentId, $departmentAgentIds)) {
            $this->logger->warning(
                __('Agent is not available for department: %1 Agent Id: %2, Ticket Id: %3',
                    $department->getName(),
                    $agentId,
                    $ticket->getId() ?? __('New')
                )
            );
            $ticket->setAgentId(AgentList::NOT_ASSIGNED_VALUE);
        }
        return empty($this->getMessages());
    }
}
