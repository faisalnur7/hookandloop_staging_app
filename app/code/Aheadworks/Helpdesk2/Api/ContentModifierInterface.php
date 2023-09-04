<?php
namespace Aheadworks\Helpdesk2\Api;

use Aheadworks\Helpdesk2\Model\Gateway\Email;

/**
 * Interface ContentModifier
 * @package Aheadworks\Helpdesk2\Api
 */
interface ContentModifierInterface
{
    /**
     * @param Email $message
     * @return Email
     */
    public function modify(Email $message);
}