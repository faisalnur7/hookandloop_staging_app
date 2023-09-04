<?php
namespace Aheadworks\Blog\Model\Import\Processor;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Api\PostRepositoryInterface;
use Aheadworks\Blog\Model\Import\MessageManager;
use Aheadworks\Blog\Api\Data\PostInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Config;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class Posts
 */
class Posts extends AbstractImport implements ImportProcessorInterface
{
    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var PostInterfaceFactory
     */
    private $postDataFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * Posts constructor.
     * @param Import $import
     * @param Config $importConfig
     * @param MessageManager $messageManager
     * @param PostRepositoryInterface $postRepository
     * @param PostInterfaceFactory $postDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param array $configEntity
     */
    public function __construct(
        Import $import,
        Config $importConfig,
        MessageManager $messageManager,
        PostRepositoryInterface $postRepository,
        PostInterfaceFactory $postDataFactory,
        DataObjectHelper $dataObjectHelper,
        array $configEntity = []
    ) {
        parent::__construct($import, $importConfig, $messageManager, $configEntity);
        $this->messageManager = $messageManager;
        $this->postRepository = $postRepository;
        $this->postDataFactory = $postDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @inheritDoc
     */
    public function saveEntity($rowData, $type = null)
    {
        $urlKey = isset($rowData['url_key']) ? $rowData['url_key'] : false;

        try {
            $postDataObject = $this->postRepository->getByUrlKey($urlKey);
        } catch (NoSuchEntityException $e) {
            $postDataObject = $this->postDataFactory->create();
        }

        $this->dataObjectHelper->populateWithArray(
            $postDataObject,
            $rowData,
            PostInterface::class
        );
        /** @var PostInterface $post */
        $this->postRepository->save($postDataObject);
    }
}