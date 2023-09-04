<?php
namespace Aheadworks\Helpdesk2\Model\Data\Provider\Form\Ticket\Thread;

/**
 * Interface ProviderInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Provider\Form
 */
interface ProviderInterface
{
    /**
     * Provide data for form
     *
     * @param int $ticketId
     * @return array
     */
    public function getData($ticketId);
}
