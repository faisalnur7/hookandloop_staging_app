<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Source\Department\AgentList as AgentListSource;

/**
 * Class General
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket
 */
class General implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        if (!$data[TicketInterface::AGENT_ID] ?? null) {
            $data[TicketInterface::AGENT_ID] = AgentListSource::NOT_ASSIGNED_VALUE;
        }
        $data[TicketInterface::TAG_NAMES] = array_values($data[TicketInterface::TAG_NAMES] ?? []);

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function prepareMetaData($meta)
    {
        return $meta;
    }
}
