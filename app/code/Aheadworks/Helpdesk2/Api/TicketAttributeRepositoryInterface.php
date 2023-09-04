<?php
namespace Aheadworks\Helpdesk2\Api;

use Magento\Framework\Api\MetadataServiceInterface;

/**
 * Ticket attribute CRUD interface
 *
 * Interface TicketAttributeRepositoryInterface
 * @api
 */
interface TicketAttributeRepositoryInterface extends MetadataServiceInterface
{
    /**
     * Retrieve specific attribute
     *
     * @param string $attributeCode
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($attributeCode);

    /**
     * Retrieve all attributes for entity type
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketAttributeSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
