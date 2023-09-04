<?php
namespace Aheadworks\Blog\ViewModel\Post\Featured;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Url;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Blog\Model\Serialize\Factory as SerializeFactory;

/**
 * Class Renderer
 */
class Renderer implements ArgumentInterface
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var SerializeFactory
     */
    private $serializer;

    /**
     * Renderer constructor.
     * @param Url $url
     * @param SerializeFactory $serializer
     */
    public function __construct(
        Url $url,
        SerializeFactory $serializer
    ) {
        $this->url = $url;
        $this->serializer = $serializer->create();
    }

    /**
     * Retrieves featured image html
     *
     * @param AbstractBlock $block
     * @param PostInterface $post
     * @return string
     */
    public function getFeaturedImageHtml($block, $post)
    {
        $html = '';
        $position = $block->getParentBlock()->getPosition();
        /** @var \Aheadworks\Blog\Block\PostImage $imageBlock */
        $imageBlock = $block->getChildBlock('aw_blog_post.' . $position . '.post_image');
        if ($imageBlock && $post) {
            $html = $imageBlock
                ->setPost($post)
                ->toHtml();
        }

        return $html;
    }

    /**
     * Retrieves script options
     *
     * @param AbstractBlock $block
     * @return bool|string
     */
    public function getScriptOptions($block)
    {
        $params = [
            'position' => $block->getPosition()
        ];

        return $this->serializer->serialize($params);
    }

    /**
     * Retrieves post url
     *
     * @param PostInterface $post
     * @return string
     */
    public function getPostUrl(PostInterface $post)
    {
        return $this->url->getPostUrl($post);
    }
}