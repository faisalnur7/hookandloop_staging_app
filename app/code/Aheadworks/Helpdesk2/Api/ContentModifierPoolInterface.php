<?php
namespace Aheadworks\Helpdesk2\Api;

use Aheadworks\Helpdesk2\Model\Gateway\Email;

/**
 * Interface ContentModifierPoolInterface
 * @package Aheadworks\Helpdesk2\Api
 */
interface ContentModifierPoolInterface
{
    /**
     * @param Email $message
     * @return Email
     */
    public function modify(Email $message);
}