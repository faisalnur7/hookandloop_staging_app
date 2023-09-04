<?php
namespace Aheadworks\Blog\Model\Post\StructuredData;

use Aheadworks\Blog\Api\Data\PostInterface;

/**
 * Interface ProviderInterface
 *
 * @package Aheadworks\Blog\Model\Post\StructuredData
 */
interface ProviderInterface
{
    /**
     * Get prepared structured data array for the blog post
     *
     * @param PostInterface $post
     * @return array
     */
    public function getData($post);
}
