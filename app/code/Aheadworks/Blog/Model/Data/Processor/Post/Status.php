<?php
namespace Aheadworks\Blog\Model\Data\Processor\Post;

use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;
use Aheadworks\Blog\Model\Source\Post\Status as PostStatus;
use Aheadworks\Blog\Api\Data\PostInterface;

/**
 * Class Status
 * @package Aheadworks\Blog\Model\Data\Processor\Post
 */
class Status implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        $saveAction = isset($data['action']) ? $data['action'] : null;

        if (in_array($saveAction, ['save_as_draft', 'save_as_draft_and_duplicate'] )) {
            $data[PostInterface::STATUS] = PostStatus::DRAFT;
        }
        if ($saveAction == 'publish') {
            $data[PostInterface::STATUS] = PostStatus::PUBLICATION;
        }
        if ($saveAction == 'schedule') {
            $data[PostInterface::STATUS] = PostStatus::SCHEDULED;
        }

        return $data;
    }
}
