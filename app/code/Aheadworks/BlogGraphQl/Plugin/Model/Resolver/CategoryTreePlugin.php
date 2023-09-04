<?php
namespace Aheadworks\BlogGraphQl\Plugin\Model\Resolver;

use Aheadworks\Blog\Model\Config;
use Magento\CatalogGraphQl\Model\Resolver\CategoryTree;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Catalog\Model\CategoryFactory;

/**
 * Class CategoryTreePlugin
 * @package Aheadworks\BlogGraphQl\Plugin\Model\Resolver
 */
class CategoryTreePlugin
{
    const DEFAULT_CATEGORY_ID = 2;
    const FIXED_BLOG_URL = 'blog';
    const RANDOM_ID = 962894;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @param Config $config
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        Config $config,
        CategoryFactory $categoryFactory
    ) {
        $this->config = $config;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @param CategoryTree $categoryTree
     * @param mixed|Value $result
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return mixed|Value
     */
    public function afterResolve(
        CategoryTree $categoryTree,
        $result,
        $field,
        $context,
        $info,
        $value = null,
        $args = null
    ) {
        if ($this->config->isBlogEnabled()
            && $this->config->isMenuLinkEnabled()
            && isset($result['id'])
            && $result['id'] == self::DEFAULT_CATEGORY_ID
        ) {
            $result['children'][self::RANDOM_ID] = [
                'id' => self::RANDOM_ID,
                'level' => 2,
                'name' => $this->config->getBlogTitle(),
                'model' => $this->categoryFactory->create(),
                'url_path' => self::FIXED_BLOG_URL,
                'url_key' => self::FIXED_BLOG_URL,
                'include_in_menu' => 1,
                'position' => self::RANDOM_ID,
                'path' => '1/2/' . self::RANDOM_ID,
                'children_count' => 0,
                'children' => []
            ];
        }
        return $result;
    }
}
