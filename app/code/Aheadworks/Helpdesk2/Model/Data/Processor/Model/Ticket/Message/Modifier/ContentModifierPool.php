<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Model\Ticket\Message\Modifier;

use Aheadworks\Helpdesk2\Api\ContentModifierInterface;
use Aheadworks\Helpdesk2\Api\ContentModifierPoolInterface;
use Aheadworks\Helpdesk2\Model\Gateway\Email;

/**
 * Class ContentModifierPool
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Model\Ticket\Message\Modifier
 */
class ContentModifierPool implements ContentModifierPoolInterface
{
    /**
     * @var array
     */
    private $modifiers;

    /**
     * ContentModifierPool constructor.
     * @param $modifiers
     */
    public function __construct(array $modifiers)
    {
        $this->modifiers = $modifiers;
    }

    /**
     * @param Email $message
     * @return Email
     */
    public function modify(Email $message)
    {
        /** @var ContentModifierInterface $modifier*/
        foreach ($this->modifiers as $modifier) {
            $message = $modifier->modify($message);
        }
        return $message;
    }
}