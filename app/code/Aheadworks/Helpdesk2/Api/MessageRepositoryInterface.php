<?php
namespace Aheadworks\Helpdesk2\Api;

/**
 * Ticket message CRUD interface
 * @api
 */
interface MessageRepositoryInterface
{
    /**
     * Save message
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\MessageInterface $message
     * @return \Aheadworks\Helpdesk2\Api\Data\MessageInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\Helpdesk2\Api\Data\MessageInterface $message);

    /**
     * Retrieve message by ID
     *
     * @param int $messageId
     * @return \Aheadworks\Helpdesk2\Api\Data\MessageInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($messageId);

    /**
     * Retrieve message list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Helpdesk2\Api\Data\MessageSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete message
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\MessageInterface $message
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\Helpdesk2\Api\Data\MessageInterface $message);

    /**
     * Delete message by ID
     *
     * @param int $messageId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($messageId);
}
