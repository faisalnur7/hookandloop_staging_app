<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\FilterArgument\EntityAttributesForAst;

use Aheadworks\BlogGraphQl\Model\Resolver\FilterArgument\EntityAttributesForAst\Version234;
use Aheadworks\BlogGraphQl\Model\Resolver\FilterArgument\EntityAttributesForAst\VersionPriorTo234;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Factory
 * @package Aheadworks\BlogGraphQl\Model\Resolver\FilterArgument\EntityAttributesForAst
 */
class Factory
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
     * @param ObjectManagerInterface $objectManager
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
     * Create email transport builder instance
     *
     * @return DataPreparerInterface
     */
    public function create()
    {
        $magentoVersion = $this->productMetadata->getVersion();
        $messageClassName = version_compare($magentoVersion, '2.3.4', '>=')
            ? Version234::class
            : VersionPriorTo234::class;

        return $this->objectManager->create($messageClassName);
    }
}