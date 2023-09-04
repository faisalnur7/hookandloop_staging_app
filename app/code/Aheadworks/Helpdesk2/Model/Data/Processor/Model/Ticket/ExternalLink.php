<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Model\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class ExternalLink
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Model\Ticket
 */
class ExternalLink implements ProcessorInterface
{
    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param EncryptorInterface $encryptor
     * @param DateTime $dateTime
     */
    public function __construct(
        EncryptorInterface $encryptor,
        DateTime $dateTime
    ) {
        $this->encryptor = $encryptor;
        $this->dateTime = $dateTime;
    }

    /**
     * Prepare model before save
     *
     * @param TicketInterface $ticket
     * @return TicketInterface
     */
    public function prepareModelBeforeSave($ticket)
    {
        if (!$ticket->getExternalLink()) {
            $hash = $this->generateHash($ticket);
            $ticket->setExternalLink($hash);
        }

        return $ticket;
    }

    /**
     * Create hash
     *
     * @param TicketInterface $ticket
     * @return string
     */
    protected function generateHash(TicketInterface $ticket)
    {
        $data = $this->dateTime->timestamp();
        $data .= $ticket->getUid();
        $data .= $ticket->getCustomerEmail();
        $data .= $ticket->getSubject();

        return $this->encryptor->hash($data);
    }

    /**
     * Prepare model after load
     *
     * @param TicketInterface $ticket
     * @return TicketInterface
     */
    public function prepareModelAfterLoad($ticket)
    {
        return $ticket;
    }
}
