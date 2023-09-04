<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\Tags;

use Aheadworks\Blog\Model\Url as BlogUrl;
use Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\ProcessorInterface;

/**
 * Class Name
 * @package Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\Tags
 */
class Name implements ProcessorInterface
{
    /**
     * @var BlogUrl
     */
    private $blogUrl;

    /**
     * @param BlogUrl $blogUrl
     */
    public function __construct(
        BlogUrl $blogUrl
    ) {
        $this->blogUrl = $blogUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function process($data, $context)
    {
        if (isset($data['filter']['url_key']['eq'])) {
            $data['filter']['name']['eq'] = $this->blogUrl->getTagNameByUrlKey($data['filter']['url_key']['eq']);
            unset ($data['filter']['url_key']);
        }
        return $data;
    }
}
