<?php
namespace Aheadworks\Blog\Model\Import\Processor;

use Aheadworks\Blog\Model\Import\MessageManager;
use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Api\Data\CategoryInterfaceFactory;
use Aheadworks\Blog\Model\ResourceModel\CategoryRepository;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Config;

/**
 * Class Categories
 */
class Categories extends AbstractImport implements ImportProcessorInterface
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var CategoryInterfaceFactory
     */
    private $categoryDataFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * Categories constructor.
     * @param Import $import
     * @param Config $importConfig
     * @param MessageManager $messageManager
     * @param CategoryRepository $categoryRepository
     * @param CategoryInterfaceFactory $categoryDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param array $configEntity
     */
    public function __construct(
        Import $import,
        Config $importConfig,
        MessageManager $messageManager,
        CategoryRepository $categoryRepository,
        CategoryInterfaceFactory $categoryDataFactory,
        DataObjectHelper $dataObjectHelper,
        array $configEntity = []
    ) {
        parent::__construct(
            $import,
            $importConfig,
            $messageManager,
            $configEntity
        );
        $this->categoryRepository = $categoryRepository;
        $this->categoryDataFactory = $categoryDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @inheritDoc
     */
    public function saveEntity($rowData, $type = null)
    {
        $urlKey = isset($rowData['url_key']) ? $rowData['url_key'] : false;

        try {
            $categoryDataObject = $this->categoryRepository->getByUrlKey($urlKey);
        } catch (NoSuchEntityException $e) {
            $categoryDataObject = $this->categoryDataFactory->create();
        }

        $this->dataObjectHelper->populateWithArray(
            $categoryDataObject,
            $rowData,
            CategoryInterface::class
        );

        $this->categoryRepository->save($categoryDataObject);
    }
}