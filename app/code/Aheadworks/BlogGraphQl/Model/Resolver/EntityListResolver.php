<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver;

use Aheadworks\BlogGraphQl\Model\Resolver\DataProvider\DataProviderInterface;
use Aheadworks\BlogGraphQl\Model\Resolver\DataProvider\SearchResult;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder;
use Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\ProcessorInterface;

/**
 * Class EntityListResolver
 * @package Aheadworks\BlogGraphQl\Model\Resolver
 */
class EntityListResolver implements ResolverInterface
{
    /**
     * @var Builder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var DataProviderInterface
     */
    protected $dataProvider;

    /**
     * @var ProcessorInterface
     */
    private $argumentProcessor;

    /**
     * @var ArgumentHelper
     */
    private $argumentHelper;

    /**
     * @param Builder $searchCriteriaBuilder
     * @param DataProviderInterface $dataProvider
     * @param ProcessorInterface $argumentProcessor
     * @param ArgumentHelper $argumentHelper
     */
    public function __construct(
        Builder $searchCriteriaBuilder,
        DataProviderInterface $dataProvider,
        ProcessorInterface $argumentProcessor,
        ArgumentHelper $argumentHelper
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dataProvider = $dataProvider;
        $this->argumentProcessor = $argumentProcessor;
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
        $args = $this->argumentProcessor->process($args, $context);
        $storeId = $this->argumentHelper->getStoreId($args);

        /** @var SearchCriteriaInterface $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder->build($field->getName(), $args);
        $searchCriteria->setCurrentPage($args['currentPage']);
        $searchCriteria->setPageSize($args['pageSize']);

        $searchResult = $this->dataProvider->getListData($searchCriteria, $storeId);

        $data = [
            'total_count' => $searchResult->getTotalCount(),
            'items' => $searchResult->getItems(),
            'page_info' => [
                'page_size' => $searchCriteria->getPageSize(),
                'current_page' => $this->resolveCurrentPage($searchCriteria, $searchResult)
            ]
        ];

        return $data;
    }

    /**
     * Resolve current page
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param SearchResult $searchResult
     * @return GraphQlInputException
     */
    protected function resolveCurrentPage($searchCriteria, $searchResult)
    {
        $maxPages = $searchCriteria->getPageSize()
            ? ceil($searchResult->getTotalCount() / $searchCriteria->getPageSize())
            : 0;

        $currentPage = $searchCriteria->getCurrentPage();
        if ($searchCriteria->getCurrentPage() > $maxPages && $searchResult->getTotalCount() > 0) {
            $currentPage = new GraphQlInputException(
                __('currentPage value %1 specified is greater than the number of pages available.', $maxPages)
            );
        }
        return $currentPage;
    }
}
