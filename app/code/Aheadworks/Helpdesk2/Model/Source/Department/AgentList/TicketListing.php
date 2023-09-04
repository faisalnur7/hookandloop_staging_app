<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Source\Department\AgentList;

use Aheadworks\Bup\Model\ResourceModel\UserProfile\CollectionExtendedFactory as UserProfileCollectionExtendedFactory;
use Aheadworks\Helpdesk2\Model\Source\Department\AgentList;
use Aheadworks\Helpdesk2\Model\Department\Search\Builder as SearchBuilder;
use Magento\Framework\Exception\LocalizedException;

class TicketListing extends AgentList
{
    /**
     * @param SearchBuilder $searchBuilder
     * @param UserProfileCollectionExtendedFactory $userProfileCollectionExtendedFactory
     */
    public function __construct(
        private SearchBuilder $searchBuilder,
        UserProfileCollectionExtendedFactory $userProfileCollectionExtendedFactory
    ) {
        parent::__construct($userProfileCollectionExtendedFactory);
    }

    /**
     * Add department ids to agent
     *
     * @return array
     * @throws LocalizedException
     */
    public function toOptionArray(): array
    {
        $options = parent::toOptionArray();

        $departmentList = $this->searchBuilder->searchDepartments();
        foreach ($departmentList as $department) {
            foreach ($options as &$option) {
                $option['department_ids'] ?? $option['department_ids'] = [];
                if (in_array($option['value'], $department->getAgentIds())
                    || $option['value'] == self::NOT_ASSIGNED_VALUE
                ) {
                    $option['department_ids'][] = $department->getId();
                }
            }
        }

        return $options;
    }
}
