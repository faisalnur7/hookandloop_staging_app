<?php
namespace Aheadworks\Blog\Model\Source\Config\Import;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Types
 */
class Types implements OptionSourceInterface
{
    const POSTS = 'blog_posts';
    const CATEGORIES = 'blog_categories';
    const AUTHORS = 'blog_authors';

    /**
     * @var array
     */
    private $options;

    /**
     * @return array[]
     */
    public function getAllOptions()
    {
        return [
            [
                'value' => self::POSTS,
                'label' => 'Posts'
            ],
            [
                'value' => self::CATEGORIES,
                'label' => 'Categories'
            ],
            [
                'value' => self::AUTHORS,
                'label' => 'Authors'
            ]
        ];
    }

    /**
     * @return array|void
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = $this->getAllOptions();
        }

        return $this->options;
    }
}