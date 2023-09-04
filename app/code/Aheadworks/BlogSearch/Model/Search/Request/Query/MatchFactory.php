<?php
declare(strict_types=1);

namespace Aheadworks\BlogSearch\Model\Search\Request\Query;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Search\Request\QueryInterface;

/**
 * Class MatchFactory
 */
class MatchFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param ObjectManagerInterface   $objectManager
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ProductMetadataInterface $productMetadata
    ) {
        $this->objectManager = $objectManager;
        $this->productMetadata = $productMetadata;
    }

    /**
     * Create match query instance
     *
     * @param array $data
     * @return QueryInterface
     */
    public function create(array $data): QueryInterface
    {
        $magentoVersion = $this->productMetadata->getVersion();
        $requestQueryMatch = version_compare($magentoVersion, '2.4.4', '>=')
            ? \Magento\Framework\Search\Request\Query\MatchQuery::class
            : \Magento\Framework\Search\Request\Query\Match::class;

        return $this->objectManager->create($requestQueryMatch, $data);
    }
}
