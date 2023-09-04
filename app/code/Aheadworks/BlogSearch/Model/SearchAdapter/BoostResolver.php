<?php
namespace Aheadworks\BlogSearch\Model\SearchAdapter;

use Aheadworks\BlogSearch\Model\Config as BlogSearchConfig;

/**
 * Class BoostResolver
 */
class BoostResolver
{
    const DEFAULT_BOOST = 1;

    /**
     * @var BlogSearchConfig
     */
    private $blogSearchConfig;

    /**
     * @var array
     */
    private $titleBoostQueryNames;

    /**
     * @var array
     */
    private $contentBoostQueryNames;

    /**
     * @var array
     */
    private $tagsBoostQueryNames;

    /**
     * @var array
     */
    private $authorBoostQueryNames;

    /**
     * @var array
     */
    private $metaTitleBoostQueryNames;

    /**
     * @var array
     */
    private $metaKeywordsBoostQueryNames;

    /**
     * @var array
     */
    private $metaDescriptionBoostQueryNames;

    /**
     * BoostResolver constructor.
     * @param BlogSearchConfig $blogSearchConfig
     * @param array $titleBoostQueryNames
     * @param array $contentBoostQueryNames
     * @param array $tagsBoostQueryNames
     * @param array $authorBoostQueryNames
     * @param array $metaTitleBoostQueryNames
     * @param array $metaKeywordsBoostQueryNames
     * @param array $metaDescriptionBoostQueryNames
     */
    public function __construct(
        BlogSearchConfig $blogSearchConfig,
        $titleBoostQueryNames = [],
        $contentBoostQueryNames = [],
        $tagsBoostQueryNames = [],
        $authorBoostQueryNames = [],
        $metaTitleBoostQueryNames = [],
        $metaKeywordsBoostQueryNames = [],
        $metaDescriptionBoostQueryNames = []
    ) {
        $this->blogSearchConfig = $blogSearchConfig;
        $this->titleBoostQueryNames = $titleBoostQueryNames;
        $this->contentBoostQueryNames = $contentBoostQueryNames;
        $this->tagsBoostQueryNames = $tagsBoostQueryNames;
        $this->authorBoostQueryNames = $authorBoostQueryNames;
        $this->metaTitleBoostQueryNames = $metaTitleBoostQueryNames;
        $this->metaKeywordsBoostQueryNames = $metaKeywordsBoostQueryNames;
        $this->metaDescriptionBoostQueryNames = $metaDescriptionBoostQueryNames;
    }

    /**
     * Resolves boost by filter name
     *
     * @param $name
     * @returns int
     */
    public function getBoostByQueryName($name)
    {
        $boost = self::DEFAULT_BOOST;

        if (in_array($name, $this->titleBoostQueryNames)) {
            $boost = $this->blogSearchConfig->getPostTitleWeight();
        } elseif (in_array($name, $this->contentBoostQueryNames)) {
            $boost = $this->blogSearchConfig->getPostContentWeight();
        } elseif (in_array($name, $this->tagsBoostQueryNames)) {
            $boost = $this->blogSearchConfig->getPostTagsWeight();
        } elseif (in_array($name, $this->authorBoostQueryNames)) {
            $boost = $this->blogSearchConfig->getPostAuthorWeight();
        } elseif (in_array($name, $this->metaTitleBoostQueryNames)) {
            $boost = $this->blogSearchConfig->getPostMetaTitleWeight();
        } elseif (in_array($name, $this->metaKeywordsBoostQueryNames)) {
            $boost = $this->blogSearchConfig->getPostMetaKeywordsWeight();
        } elseif (in_array($name, $this->metaDescriptionBoostQueryNames)) {
            $boost = $this->blogSearchConfig->getPostMetaDescriptionWeight();
        }

        return $boost;
    }

    /**
     * Returns filter names with defined boost
     *
     * @return string[]
     */
    public function getBoostedQueryNames()
    {
        return array_merge(
            $this->titleBoostQueryNames,
            $this->contentBoostQueryNames,
            $this->tagsBoostQueryNames,
            $this->authorBoostQueryNames,
            $this->metaTitleBoostQueryNames,
            $this->metaKeywordsBoostQueryNames,
            $this->metaDescriptionBoostQueryNames
        );
    }
}
