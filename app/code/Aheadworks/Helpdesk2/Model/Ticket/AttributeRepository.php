<?php
namespace Aheadworks\Helpdesk2\Model\Ticket;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Eav\Model\AttributeRepository as EavAttributeRepository;
use Aheadworks\Helpdesk2\Api\TicketAttributeRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Ticket as TicketModel;

/**
 * Class AttributeRepository
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket
 */
class AttributeRepository implements TicketAttributeRepositoryInterface
{
    /**
     * @var EavAttributeRepository
     */
    private $eavAttributeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param EavAttributeRepository $eavAttributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        EavAttributeRepository $eavAttributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->eavAttributeRepository = $eavAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    public function get($attributeCode)
    {
        return $this->eavAttributeRepository->get(TicketModel::ENTITY, $attributeCode);
    }

    /**
     * @inheritdoc
     *
     * @throws InputException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        return $this->eavAttributeRepository->getList(TicketModel::ENTITY, $searchCriteria);
    }

    /**
     * @inheritdoc
     *
     * @throws InputException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getCustomAttributesMetadata($dataObjectClassName = null)
    {
        return $this->getList($this->searchCriteriaBuilder->create())->getItems();
    }
}
