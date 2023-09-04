<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Model\Ticket\Message\Modifier\Pool;

use Aheadworks\Helpdesk2\Api\ContentModifierInterface;
use Aheadworks\Helpdesk2\Model\Gateway\Email;

/**
 * Class RemoveReply
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Model\Ticket\Message\Modifier\Pool
 */
class RemoveReply implements ContentModifierInterface
{
    /**
     * @var string
     */
    private $pattern = '/^<[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}>/';

    /**
     * @param Email $message
     * @return Email
     */
    public function modify(Email $message)
    {
        $lines = explode("\n", (string)$message->getBody());
        $messageLines = [];
        foreach ($lines as $line) {
            if (!preg_match($this->pattern, (string)$line)) {
                $messageLines[] = $line;
            } else {
                array_pop($messageLines);
                break;
            }
        }
        $message->setBody(implode("\n", $messageLines));
        return $message;
    }
}