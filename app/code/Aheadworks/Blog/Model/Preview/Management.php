<?php
namespace Aheadworks\Blog\Model\Preview;

use Aheadworks\Blog\Api\PostPreviewManagementInterface;
use Aheadworks\Blog\Api\PostPreviewRepositoryInterface;
use Aheadworks\Blog\Api\Data\PostPreviewInterface;
use Aheadworks\Blog\Api\Data\PostPreviewInterfaceFactory;
use Magento\Framework\Serialize\Serializer\Json as Serializer;


class Management implements PostPreviewManagementInterface
{
    /**
     * @var PostPreviewRepositoryInterface
     */
    private $postPreviewRepository;

    /**
     * @var PostPreviewInterfaceFactory
     */
    private $postPreviewFactory;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @param PostPreviewRepositoryInterface $postPreviewRepository
     * @param PostPreviewInterfaceFactory $postPreviewFactory
     * @param Serializer $serializer
     */
    public function __construct(
        PostPreviewRepositoryInterface $postPreviewRepository,
        PostPreviewInterfaceFactory $postPreviewFactory,
        Serializer $serializer
    ) {
        $this->postPreviewRepository = $postPreviewRepository;
        $this->postPreviewFactory = $postPreviewFactory;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function savePreviewData($data)
    {
        $serializedData = $this->serializer->serialize($data);

        /** @var PostPreviewInterface $postPreview */
        $postPreview = $this->postPreviewFactory->create();
        $postPreview->setPostPreviewData($serializedData);
        $postPreview = $this->postPreviewRepository->save($postPreview);

        return $postPreview->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviewData($key)
    {
        /** @var PostPreviewInterface $postPreview */
        $postPreview = $this->postPreviewRepository->get($key);
        $serializedData = $postPreview->getPostPreviewData();
        $data = (array)$this->serializer->unserialize($serializedData);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function withdrawPreviewData($key)
    {
        $data = $this->getPreviewData($key);
        $this->postPreviewRepository->deleteById($key);

        return $data;
    }
}
