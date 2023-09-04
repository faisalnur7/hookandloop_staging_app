<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier;

use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\SenderResolverInterface as SenderResolver;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;

class SenderResolve implements SenderResolver
{
    /**
     * @var SenderResolverInterface
     */
    private $senderResolver;

    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @param SenderResolverInterface $senderResolver
     * @param DepartmentRepositoryInterface $departmentRepository
     */
    public function __construct(
        SenderResolverInterface $senderResolver,
        DepartmentRepositoryInterface $departmentRepository
    ) {
        $this->senderResolver = $senderResolver;
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * Get email and name agent
     *
     * @param EventDataInterface $eventData
     * @return array
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function resolve(EventDataInterface $eventData): array
    {
        $senderData = $this->senderResolver->resolve('support', $eventData->getTicket()->getStoreId());
        $department = $this->departmentRepository->get($eventData->getTicket()->getDepartmentId());
        $email = !empty($department->getEmailToSendFrom())
            ? $department->getEmailToSendFrom()
            : $senderData['email'] ?? '';
        $name = $senderData['name'] ?? '';
        return [$email, $name];
    }
}
