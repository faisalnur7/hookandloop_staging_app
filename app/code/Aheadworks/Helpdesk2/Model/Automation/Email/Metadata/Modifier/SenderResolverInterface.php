<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier;

use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;

interface SenderResolverInterface
{
    /**
     * Get email and name agent
     *
     * @param EventDataInterface $eventData
     * @return array
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function resolve(EventDataInterface $eventData): array;
}
