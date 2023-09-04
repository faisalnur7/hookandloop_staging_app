<?php
namespace Aheadworks\Blog\Ui\DataProvider;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\ResourceModel\Post\Grid\CollectionFactory;
use Aheadworks\Blog\Model\Source\Post\AuthorDisplayMode;
use Aheadworks\Blog\Model\Source\Post\Status;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Aheadworks\Blog\Model\ResourceModel\Post\Collection;
use Aheadworks\Blog\Model\Post\FeaturedImageInfo;

/**
 * Post data provider
 */
class PostDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
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
     * @var FeaturedImageInfo
     */
    private $imageInfo;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
     * @param FeaturedImageInfo $imageInfo
     * @param Config $config
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
        FeaturedImageInfo $imageInfo,
        Config $config,
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
        $this->collection = $collectionFactory->create()
            ->setFlag(Collection::IS_NEED_TO_ATTACH_RELATED_PRODUCT_IDS, false)
        ;
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
        $this->imageInfo = $imageInfo;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = [];
        $dataFromForm = $this->dataPersistor->get('aw_blog_post');
        if (!empty($dataFromForm)) {
            $object = $this->collection->getNewEmptyItem();
            $object->setData($dataFromForm);
            $data[$object->getId()] = $this->preparePersistingFormData($object->getData());
            $this->dataPersistor->clear('aw_blog_post');
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            /** @var \Aheadworks\Blog\Model\Post $post */
            foreach ($this->getCollection()->getItems() as $post) {
                if ($id == $post->getId()) {
                    $data[$id] = $this->prepareFormData($post->getData());
                }
            }
        }
        return $data;
    }

    /**
     * Prepare form data
     *
     * @param array $itemData
     * @return array
     */
    private function prepareFormData(array $itemData)
    {
        $itemData['is_published'] = $itemData['status'] == Status::PUBLICATION ? 1 : 0;
        $itemData['is_scheduled'] = $itemData['status'] == Status::SCHEDULED ? 1 : 0;
        $itemData['has_short_content'] = !empty($itemData['short_content']);
        $itemData['tag_names'] = array_values($itemData['tag_names']);
        $itemData = $this->prepareImageData($itemData);
        $itemData = $this->prepareImageMobileData($itemData);
        $itemData = $this->prepareUseDefaultData($itemData);
        $itemData = $this->prepareAuthorDisplayMode($itemData);

        return $itemData;
    }

    /**
     * Prepare Is Author Displayed Data
     *
     * @param array $itemData
     * @return array
     */
    private function prepareAuthorDisplayMode(array $itemData)
    {
        if ($itemData['isAuthorDisplayModeUseDefault']) {
            /** (string)(int) because the strict comparison in the getReverseValueMap method */
            $itemData[PostInterface::AUTHOR_DISPLAY_MODE] = (string)(int)$this->config->areAuthorsDisplayed();
        } else {
            $itemData[PostInterface::AUTHOR_DISPLAY_MODE] = $itemData[PostInterface::AUTHOR_DISPLAY_MODE] == AuthorDisplayMode::USE_DEFAULT_OPTION
                ? AuthorDisplayMode::DISPLAY_NONE
                : $itemData[PostInterface::AUTHOR_DISPLAY_MODE];
        }

        return $itemData;
    }


    /**
     * Prepare persisting data
     *
     * @param array $itemData
     * @return array
     */
    private function preparePersistingFormData(array $itemData)
    {
        $itemData = $this->prepareImageData($itemData);
        $itemData = $this->prepareImageMobileData($itemData);
        $itemData = $this->prepareUseDefaultData($itemData);
        return $itemData;
    }

    /**
     * Prepare featured image data
     *
     * @param array $itemData
     * @return array
     */
    private function prepareImageData(array $itemData)
    {
        if (!empty($itemData['featured_image_file'])) {
            $itemData['featured_image_file'] = [
                0 => [
                    'name' => $this->imageInfo->getImageFileName($itemData['featured_image_file']),
                    'url' => $this->imageInfo->getImageUrl($itemData['featured_image_file']),
                    'path' => $itemData['featured_image_file']
                ]
            ];
        }
        return $itemData;
    }

    /**
     * Prepare featured image mobile data
     *
     * @param array $itemData
     * @return array
     */
    private function prepareImageMobileData(array $itemData)
    {
        if (!empty($itemData[PostInterface::FEATURED_IMAGE_MOBILE_FILE])) {
            $itemData[PostInterface::FEATURED_IMAGE_MOBILE_FILE] = [
                0 => [
                    'name' => $this->imageInfo->getImageFileName($itemData[PostInterface::FEATURED_IMAGE_MOBILE_FILE]),
                    'url' => $this->imageInfo->getImageUrl($itemData[PostInterface::FEATURED_IMAGE_MOBILE_FILE]),
                    'path' => $itemData[PostInterface::FEATURED_IMAGE_MOBILE_FILE]
                ]
            ];
        }
        return $itemData;
    }

    /**
     * Prepare use default checkboxes
     *
     * @param array $itemData
     * @return array
     */
    private function prepareUseDefaultData(array $itemData)
    {
        $itemData['twitterSiteUseDefault'] = $itemData['meta_twitter_site'] ? false : true;
        $itemData['isAuthorDisplayModeUseDefault'] = $itemData[PostInterface::AUTHOR_DISPLAY_MODE] == AuthorDisplayMode::USE_DEFAULT_OPTION;

        return $itemData;
    }
}
