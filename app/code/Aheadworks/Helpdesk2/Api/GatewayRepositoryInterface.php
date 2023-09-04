<?php
namespace Aheadworks\Helpdesk2\Api;

/**
 * Gateway CRUD interface
 * @api
 */
interface GatewayRepositoryInterface
{
    /**
     * Save gateway
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface $gateway
     * @return \Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface $gateway);

    /**
     * Retrieve gateway by ID
     *
     * @param int $gatewayId
     * @param bool $reload returns non cached version of gateway
     * @return \Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($gatewayId, $reload = false);

    /**
     * Retrieve gateway list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Helpdesk2\Api\Data\GatewayDataSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete gateway
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface $gateway
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface $gateway);

    /**
     * Delete gateway by ID
     *
     * @param int $gatewayId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($gatewayId);
}
