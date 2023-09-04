<?php
namespace Aheadworks\BlogSearch\Block;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Block\Post as PostBlock;
use Magento\Framework\View\Element\Template;

/**
 * Class PostList
 * @method string getSocialIconsBlock()
 */
class PostList extends Template
{
    /**
     * Retrieves list item html
     *
     * @param string $blockAlias
     * @param PostInterface $post
     * @return string
     */
    public function getItemHtml(string $blockAlias, PostInterface $post)
    {
        $html = '';

        /** @var PostBlock $block */
        $block = $this->getChildBlock($blockAlias);
        if ($block) {
            $block->setPost($post);
            $block->setMode(PostBlock::MODE_LIST_ITEM);

            $html = $block->toHtml();
        }

        return $html;
    }
}
