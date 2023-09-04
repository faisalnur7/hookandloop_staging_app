<?php
namespace Aheadworks\Blog\Model\Service;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Api\Data\TagInterface;
use Aheadworks\Blog\Api\TagManagementInterface;
use Aheadworks\Blog\Api\TagRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class TagService implements TagManagementInterface
{
    /**
     * @var TagRepositoryInterface
     */
    private $tagRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param TagRepositoryInterface $tagRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        TagRepositoryInterface $tagRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger
    ) {
        $this->tagRepository = $tagRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
    }

    /**
     * inheritdoc
     */
    public function getPostTags(PostInterface $post)
    {
        $this->searchCriteriaBuilder->addFilter(TagInterface::NAME, $post->getTagNames(), 'in');

        try {
            $tags = $this->tagRepository
                ->getList($this->searchCriteriaBuilder->create())
                ->getItems();
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
            $tags = [];
        }

         return $tags;
    }
}
