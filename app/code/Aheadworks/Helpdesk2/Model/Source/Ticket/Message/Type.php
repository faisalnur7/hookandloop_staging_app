<?php
namespace Aheadworks\Helpdesk2\Model\Source\Ticket\Message;

/**
 * Class Type
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Ticket\Message
 */
class Type
{
    /**
     * Message types
     */
    const ADMIN = 'admin-message';
    const CUSTOMER = 'customer-message';
    const INTERNAL = 'admin-internal';
    const SYSTEM = 'system-message';
    const TICKET_LOCK = 'ticket-lock-message';
    const ESCALATION = 'escalation-message';
}
