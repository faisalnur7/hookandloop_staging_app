<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;

/**
 * Class Attachments
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket
 */
class Attachments implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        if (isset($data[MessageInterface::ATTACHMENTS])) {
            $processedAttachments = [];
            foreach ($data[MessageInterface::ATTACHMENTS] as $attachment) {
                $processedAttachments[] = [
                    'file_name' => $attachment['name'] ?? $attachment['file_name'] ?? '',
                    'file_path' => $attachment['file'] ?? $attachment['file_path'] ?? '',
                ];
            }
            $data[MessageInterface::ATTACHMENTS] = $processedAttachments;
        }

        return $data;
    }
}
