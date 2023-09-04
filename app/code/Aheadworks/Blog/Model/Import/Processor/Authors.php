<?php
namespace Aheadworks\Blog\Model\Import\Processor;

use Aheadworks\Blog\Api\AuthorRepositoryInterface;
use Aheadworks\Blog\Api\Data\AuthorInterface;
use Aheadworks\Blog\Api\Data\AuthorInterfaceFactory;
use Aheadworks\Blog\Model\Import\MessageManager;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Config;

/**
 * Class Authors
 */
class Authors extends AbstractImport implements ImportProcessorInterface
{
    /**
     * @var AuthorRepositoryInterface
     */
    private $authorRepository;

    /**
     * @var AuthorInterfaceFactory
     */
    private $authorDataFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var array
     */
    private $configEntity;

    /**
     * Authors constructor.
     * @param Import $import
     * @param Config $importConfig
     * @param MessageManager $messageManager
     * @param AuthorRepositoryInterface $authorRepository
     * @param AuthorInterfaceFactory $authorDataFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        Import $import,
        Config $importConfig,
        MessageManager $messageManager,
        AuthorRepositoryInterface $authorRepository,
        AuthorInterfaceFactory $authorDataFactory,
        DataObjectHelper $dataObjectHelper,
        array $configEntity = []
    ) {
        parent::__construct($import, $importConfig, $messageManager, $configEntity);
        $this->authorRepository = $authorRepository;
        $this->authorDataFactory = $authorDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->configEntity = $configEntity;
    }

    /**
     * @inheritDoc
     */
    public function saveEntity($rowData, $type = null)
    {
        $id = isset($rowData['id']) && $rowData['id'] ? $rowData['id'] : false;

        try {
            $authorDataObject = $this->authorRepository->get($id);
        } catch (NoSuchEntityException $e) {
            $authorDataObject = $this->authorDataFactory->create();
        }

        $this->dataObjectHelper->populateWithArray(
            $authorDataObject,
            $rowData,
            AuthorInterface::class
        );

        $this->authorRepository->save($authorDataObject);
    }
}