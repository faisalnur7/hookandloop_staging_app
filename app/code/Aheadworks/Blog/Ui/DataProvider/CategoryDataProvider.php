<?php
namespace Aheadworks\Blog\Ui\DataProvider;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Model\ResourceModel\Category\Grid\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Category data provider
 */
class CategoryDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var PoolInterface
     */
    private $modifierPool;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
     * @param PoolInterface $modifierPool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        PoolInterface $modifierPool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
        $this->modifierPool = $modifierPool;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = [];
        $dataFromForm = $this->dataPersistor->get('aw_blog_category');
        $id = $this->request->getParam($this->getRequestFieldName());
        if (!empty($dataFromForm)) {
            $object = $this->collection->getNewEmptyItem();
            $object->setData($dataFromForm);
            $data = $object->getData();
            $this->dataPersistor->clear('aw_blog_category');
        } else {
            /** @var \Aheadworks\Blog\Model\Category $category */
            foreach ($this->getCollection()->getItems() as $category) {
                if ($id == $category->getId()) {
                    $data = $category->getData();
                }
            }
            if ($parentId = $this->request->getParam('parent', 0)) {
                $data[CategoryInterface::PARENT_ID] = $parentId;
            }
        }

        $preparedData[$id] = $data ? $this->prepareFormData($data) : null;
        return $preparedData;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->modifierPool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }

    /**
     * Prepare form data
     *
     * @param array $itemData
     * @return array
     * @throws LocalizedException
     */
    private function prepareFormData($itemData)
    {
        /** @var ModifierInterface $modifier */
        foreach ($this->modifierPool->getModifiersInstances() as $modifier) {
            $itemData = $modifier->modifyData($itemData);
        }

        return $itemData;
    }
}
