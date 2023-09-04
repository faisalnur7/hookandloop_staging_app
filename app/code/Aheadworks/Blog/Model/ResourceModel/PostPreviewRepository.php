<?php
namespace Aheadworks\Blog\Model\ResourceModel;

use Aheadworks\Blog\Api\PostPreviewRepositoryInterface;
use Aheadworks\Blog\Api\Data\PostPreviewInterface;
use Aheadworks\Blog\Api\Data\PostPreviewInterfaceFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class PostPreviewRepository
 * @package Aheadworks\Blog\Model\ResourceModel
 */
class PostPreviewRepository implements PostPreviewRepositoryInterface
{
    /**
     * @var PostPreviewInterface[]
     */
    private $instances = [];

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var PostPreviewInterfaceFactory
     */
    private $postPreviewFactory;

    /**
     * @param EntityManager $entityManager
     * @param PostPreviewInterfaceFactory $postPreviewFactory
     */
    public function __construct(
        EntityManager $entityManager,
        PostPreviewInterfaceFactory $postPreviewFactory
    ) {
        $this->entityManager = $entityManager;
        $this->postPreviewFactory = $postPreviewFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(PostPreviewInterface $postPreview)
    {
        try {
            $this->entityManager->save($postPreview);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        unset($this->instances[$postPreview->getId()]);
        return $this->get($postPreview->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function get($postPreviewId)
    {
        if (!isset($this->instances[$postPreviewId])) {
            /** @var PostPreviewInterface $request */
            $request = $this->postPreviewFactory->create();
            $this->entityManager->load($request, $postPreviewId);
            if (!$request->getId()) {
                throw NoSuchEntityException::singleField('id', $postPreviewId);
            }
            $this->instances[$postPreviewId] = $request;
        }
        return $this->instances[$postPreviewId];
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($postPreviewId)
    {
        /** @var PostPreviewInterface $request */
        $request = $this->postPreviewFactory->create();
        $this->entityManager->load($request, $postPreviewId);
        if (!$request->getId()) {
            throw NoSuchEntityException::singleField('id', $postPreviewId);
        }
        $this->entityManager->delete($request);
        unset($this->instances[$postPreviewId]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(PostPreviewInterface $postPreview)
    {
        return $this->deleteById($postPreview->getId());
    }
}
