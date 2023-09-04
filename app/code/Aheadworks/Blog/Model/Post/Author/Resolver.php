<?php
namespace Aheadworks\Blog\Model\Post\Author;

use Aheadworks\Blog\Api\Data\AuthorInterface;
use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Api\AuthorRepositoryInterface;
use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\Source\Post\AuthorDisplayMode;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Resolver
 * @package Aheadworks\Blog\Model\Post\Author
 */
class Resolver
{
    /**
     * @var AuthorRepositoryInterface
     */
    private $authorRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Creator
     */
    private $creator;

    /**
     * @var array
     * $authorName => $authorId pairs
     */
    private $authorIds = [];

    /**
     * @var Config
     */
    private $config;

    /**
     * @param AuthorRepositoryInterface $authorRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Config $config
     * @param Creator $creator
     */
    public function __construct(
        AuthorRepositoryInterface $authorRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Config $config,
        Creator $creator
    ) {
        $this->authorRepository = $authorRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->creator = $creator;
        $this->config = $config;
    }

    /**
     * Retrieve post author full name
     *
     * @param PostInterface $post
     * @return string
     */
    public function getFullName($post)
    {
        $fullName = '';
        if ($author = $post->getAuthor()) {
            $fullName = $author->getFirstname() . ' ' . $author->getLastname();
        }
        return $fullName;
    }

    /**
     * Resolve author id
     *
     * @param array $postData
     * @param string $authorNameKey
     * @return int|null
     */
    public function resolveId($postData, $authorNameKey)
    {
        $this->initAuthorIds();
        $postAuthorName = isset($postData[$authorNameKey]) ? $postData[$authorNameKey] : '';

        return $this->retrieveId($postAuthorName);
    }

    /**
     * Resolve author id for Wordpress
     *
     * @param string $postAuthorName
     * @return int|null
     */
    public function resolveIdForWp($postAuthorName)
    {
        $this->initAuthorIds();

        return $this->retrieveId($postAuthorName);
    }

    /**
     * Whether the author should be displayed
     *
     * @param PostInterface $post
     * @param int $storeId
     * @return bool
     */
    public function resolveToDisplayAuthor($post, $storeId)
    {
        return $post->getAuthor()
            && ($post->getAuthorDisplayMode() == AuthorDisplayMode::USE_DEFAULT_OPTION
            ? $this->config->areAuthorsDisplayed($storeId)
            : $post->getAuthorDisplayMode());
    }

    /**
     * Retrieve ID
     *
     * @param string $postAuthorName
     * @return int|null
     */
    private function retrieveId($postAuthorName)
    {
        $authorId = null;
        try {
            $key = str_replace(' ', '_', $postAuthorName);
            if (array_key_exists($key, $this->authorIds)) {
                $authorId = $this->authorIds[$key];
            } else {
                $authorId = $this->creator->createByName($postAuthorName)->getId();
                $this->authorIds[$key] = $authorId;
            }
        } catch (\Exception $e) {
        }

        return $authorId;
    }

    /**
     * Init author ids
     */
    private function initAuthorIds()
    {
        if (empty($this->authorIds)) {
            $authors = $this->authorRepository->getList($this->searchCriteriaBuilder->create())->getItems();

            /** @var AuthorInterface $author */
            foreach ($authors as $author) {
                $authorName = $author->getFirstname() . '_' . $author->getLastname();
                $this->authorIds[$authorName] = $author->getId();
            }
        }
    }
}
