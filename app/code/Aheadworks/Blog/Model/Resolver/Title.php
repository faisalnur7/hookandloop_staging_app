<?php
namespace Aheadworks\Blog\Model\Resolver;

use Aheadworks\Blog\Api\Data\AuthorInterface;
use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Config;

/**
 * Class Title
 * @package Aheadworks\Blog\Model\Resolver
 */
class Title
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Get title by entity
     *
     * @param PostInterface|CategoryInterface|AuthorInterface $entity
     * @return string
     */
    public function getTitle($entity)
    {
        $title = '';
        if ($entity->getMetaTitle()) {
            $title = $entity->getMetaTitle();
        } else {
            switch (true) {
                case $entity instanceof PostInterface:
                    $title = $entity->getTitle();
                    break;
                case $entity instanceof CategoryInterface:
                    $title = $entity->getName();
                    break;
                case $entity instanceof AuthorInterface:
                    $title = $entity->getFirstname() . ' ' . $entity->getLastname();
                    break;
            }
        }

        $title = trim($title);
        $prefix = $entity->getMetaPrefix() ?: $this->config->getTitlePrefix();
        $suffix = $entity->getMetaSuffix() ?: $this->config->getTitleSuffix();

        return
            (empty($prefix) ? '' : ($prefix . ' | '))
            . trim($title)
            . (empty($suffix) ? '' : (' | ' . $suffix));
    }
}
