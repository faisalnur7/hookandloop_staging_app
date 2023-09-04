<?php
namespace Aheadworks\Helpdesk2\Model\Rejection\Processor;

use Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Rejection\Processor
 */
interface ProcessorInterface
{
    /**
     * @param RejectedMessageInterface $rejectedMessage
     * @return bool
     */
    public function process($rejectedMessage);
}
