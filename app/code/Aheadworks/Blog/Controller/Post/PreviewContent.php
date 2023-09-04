<?php
namespace Aheadworks\Blog\Controller\Post;

use Aheadworks\Blog\Controller\Action;

/**
 * Class PreviewContent
 * @package Aheadworks\Blog\Controller\Post
 */
class PreviewContent extends Action
{
    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
