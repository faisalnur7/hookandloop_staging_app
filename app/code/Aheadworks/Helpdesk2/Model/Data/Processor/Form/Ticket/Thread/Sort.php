<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket\Thread;

use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;

/**
 * Class Sort
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket\Thread
 */
class Sort implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        usort($data['items'], function ($a, $b) {
            return $a['created_at'] <=> $b['created_at'];
        });

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function prepareMetaData($meta)
    {
        return $meta;
    }
}
