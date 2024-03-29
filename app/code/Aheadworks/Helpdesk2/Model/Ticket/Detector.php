<?php
namespace Aheadworks\Helpdesk2\Model\Ticket;

use Aheadworks\Helpdesk2\Model\Ticket\Detector\DetectorInterface;

/**
 * Class Detector
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket
 */
class Detector
{
    const NEW_TICKET_TYPE = 'new_ticket';
    const TICKET_UPDATED_TYPE = 'ticket_updated';
    const TICKET_ESCALATED_TYPE = 'ticket_escalated';
    const NEW_MESSAGE_TYPE = 'new_message';

    /**
     * @var array
     */
    private $typeObjects;

    /**
     * @param array $typeObjects
     */
    public function __construct(
        array $typeObjects
    ) {
        $this->typeObjects = $typeObjects;
    }

    /**
     * Detect ticket changes depending on type
     *
     * @param array $dataToDetect
     * @param string $type
     * @return bool
     */
    public function detect($dataToDetect, $type)
    {
        if (isset($this->typeObjects)) {
            /** @var DetectorInterface $detectorComposite */
            $detectorComposite = $this->typeObjects[$type];
            $detectorComposite->detect($dataToDetect);

            return true;
        }

        return false;
    }
}
