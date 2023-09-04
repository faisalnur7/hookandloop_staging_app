<?php
namespace Aheadworks\Helpdesk2\Api;

/**
 * RejectedMessage CRUD interface
 * @api
 */
interface RejectedMessageRepositoryInterface
{
    /**
     * Save message
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface $rejectedMessage
     * @return \Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface $rejectedMessage);

    /**
     * Retrieve message by ID
     *
     * @param int $id
     * @return \Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Retrieve message list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Helpdesk2\Api\Data\RejectedMessageSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete message
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface $rejectedMessage
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface $rejectedMessage);
}
