<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\GroupManagement;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Aheadworks\Blog\Api\PostRepositoryInterface;
use Aheadworks\Blog\Api\Data\PostInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;

/**
 * Class PostWithRelatedPosts
 * @package Aheadworks\BlogGraphQl\Model\Resolver
 */
class PostWithRelatedPosts implements ResolverInterface
{
    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var GetCustomer
     */
    private $getCustomer;

    /**
     * @var ArgumentHelper
     */
    private $argumentHelper;

    /**
     * PostWithRelatedPosts constructor.
     * @param PostRepositoryInterface $postRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param GetCustomer $getCustomer
     * @param ArgumentHelper $argumentHelper
     */
    public function __construct(
        PostRepositoryInterface $postRepository,
        DataObjectProcessor $dataObjectProcessor,
        GetCustomer $getCustomer,
        ArgumentHelper $argumentHelper
    ) {
        $this->postRepository = $postRepository;
        $this->dataObjectProcessor =$dataObjectProcessor;
        $this->getCustomer = $getCustomer;
        $this->argumentHelper = $argumentHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $postId = isset($args['postId']) ? $args['postId'] : null;
        $storeId = $this->argumentHelper->getStoreId($args);

        try {
            /** @var CustomerInterface $customer */
            $customer = $this->getCustomer->execute($context);
            $customerGroupId = $customer->getGroupId();
        } catch (\Exception $e) {
            $customerGroupId = GroupManagement::NOT_LOGGED_IN_ID;
        }

        $result = [];
        if (!is_null($storeId) && !is_null($postId) && !is_null($customerGroupId)) {
            $post = $this->postRepository->getWithRelatedPosts($postId, $storeId, $customerGroupId);
            $result = $this->dataObjectProcessor->buildOutputDataArray($post, PostInterface::class);
        }

        return $result;
    }
}
