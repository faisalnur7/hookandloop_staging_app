<?php
namespace Aheadworks\Blog\Api;

/**
 * Interface PostPreviewManagementInterface
 * @package Aheadworks\Blog\Api
 */
interface PostPreviewManagementInterface
{
    /**
     * Save preview data
     *
     * @param array $data
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function savePreviewData($data);

    /**
     * Get preview data
     *
     * @param int $key
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPreviewData($key);

    /**
     * Withdraw(get and delete) preview data
     *
     * @param int $key
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function withdrawPreviewData($key);
}
