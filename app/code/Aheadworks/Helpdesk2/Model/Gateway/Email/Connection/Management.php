<?php
namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Connection;

use Zend\Mail\Protocol\Exception\RuntimeException;
use Zend\Mail\Storage\Message as MailMessage;

/**
 * Class Management
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Connection
 */
class Management
{
    /**
     * Get mail UIDs
     *
     * @param object $connection
     * @return array
     */
    public function getMailUIDs($connection)
    {
        return $connection->getUniqueId();
    }

    /**
     * Get message number by UID
     *
     * @param object $connection
     * @param string $mailUid
     * @return int
     */
    public function getMessageNumberByMailUid($connection, $mailUid)
    {
        return $connection->getNumberByUniqueId($mailUid);
    }

    /**
     * Get message by number
     *
     * @param object $connection
     * @param int $number
     * @return MailMessage
     * @throws RuntimeException
     */
    public function getMessageByNumber($connection, $number)
    {
        return $connection->getMessage($number);
    }

    /**
     * Get message headers by number
     *
     * @param object $connection
     * @param string $number
     * @return string
     */
    public function getMessageHeadersByNumber($connection, $number)
    {
        return $connection->getRawHeader($number);
    }

    /**
     * Remove message from server by number
     *
     * @param object $connection
     * @param string $number
     * @return bool
     */
    public function removeMessageFromServerByNumber($connection, $number)
    {
        $connection->removeMessage($number);
        return true;
    }
}
