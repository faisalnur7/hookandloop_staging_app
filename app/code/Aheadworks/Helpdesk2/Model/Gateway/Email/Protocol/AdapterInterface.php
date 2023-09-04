<?php
namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Protocol;

use Zend\Mail\Protocol\Imap;
use Zend\Mail\Protocol\Pop3;

/**
 * Interface AdapterInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Protocol
 */
interface AdapterInterface
{
    const OAUTH_NAME = 'XOAUTH2';

    /**
     * Send request
     *
     * @param string $xoauthString
     * @return bool
     */
    public function sendRequest($xoauthString);

    /**
     * Get protocol
     *
     * @return Imap|Pop3
     */
    public function getProtocol();
}
