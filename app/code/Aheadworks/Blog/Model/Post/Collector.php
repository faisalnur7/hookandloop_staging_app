<?php
declare(strict_types=1);

namespace Aheadworks\Blog\Model\Post;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Date;
use Aheadworks\Blog\Model\Post\Listing\Builder as PostListBuilder;
use Aheadworks\Blog\Model\Source\Post\Status;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Collector
 */
class Collector
{
    /**
     * @var PostListBuilder
     */
    private $postListBuilder;

    /**
     * @var Date
     */
    private $date;

    /**
     * @param PostListBuilder $postListBuilder
     * @param Date $date
     */
    public function __construct(
        PostListBuilder $postListBuilder,
        Date        $date
    )
    {
        $this->postListBuilder = $postListBuilder;
        $this->date = $date;
    }

    /**
     * Collect scheduled posts
     *
     * @return PostInterface[]
     * @throws LocalizedException
     */
    public function collectScheduledPosts(): array
    {
        $now = $this->date->getCurrentDate(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);

        $this->postListBuilder
            ->getSearchCriteriaBuilder()
            ->addFilter(PostInterface::STATUS, Status::SCHEDULED)
            ->addFilter(PostInterface::PUBLISH_DATE, $now, 'lteq');

        return $this->postListBuilder->getPostList();
    }

    /**
     * Collect expired posts
     *
     * @return PostInterface[]
     * @throws LocalizedException
     */
    public function collectExpiredPosts(): array
    {
        $now = $this->date->getCurrentDate(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);

        $this->postListBuilder
            ->getSearchCriteriaBuilder()
            ->addFilter(PostInterface::STATUS, Status::PUBLICATION)
            ->addFilter(PostInterface::END_DATE, $now, 'lteq');

        return $this->postListBuilder->getPostList();
    }
}
