<?php
namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Processor;

/**
 * Class EmailParser
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Processor
 */
class EmailParser
{
    /**
     * Parse customer email from mail subject
     *
     * @param string $fromSubject
     * @return bool|string
     */
    public function parseFromSubject($fromSubject)
    {
        if (preg_match("/([a-z0-9.\-_]+@[a-z0-9.\-_]+\.[a-z0-9.\-_]+)/i", (string)$fromSubject, $matches)) {
            if (isset($matches[1])) {
                return strtolower((string)$matches[1]);
            }
        }

        return false;
    }
}
