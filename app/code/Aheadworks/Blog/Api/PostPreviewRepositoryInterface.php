<?php
namespace Aheadworks\Blog\Api;

/**
 * Interface PostPreviewRepositoryInterface
 * @package Aheadworks\Blog\Api
 */
interface PostPreviewRepositoryInterface
{
    /**
     * Save post preview
     *
     * @param \Aheadworks\Blog\Api\Data\PostPreviewInterface $postPreview
     * @return \Aheadworks\Blog\Api\Data\PostPreviewInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Blog\Api\Data\PostPreviewInterface $postPreview);

    /**
     * Retrieve post preview
     *
     * @param int $postPreviewId
     * @return \Aheadworks\Blog\Api\Data\PostPreviewInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($postPreviewId);

    /**
     * Delete post preview by ID
     *
     * @param int $postPreviewId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($postPreviewId);

    /**
     * Delete post preview
     *
     * @param \Aheadworks\Blog\Api\Data\PostPreviewInterface $postPreview
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function delete(\Aheadworks\Blog\Api\Data\PostPreviewInterface $postPreview);
}
