<?php
namespace Aheadworks\Bup\Model\UserProfile;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SortOrderBuilder;
use Aheadworks\Bup\Api\Data\UserProfileMetadataInterface;
use Aheadworks\Bup\Api\UserProfileMetadataRepositoryInterface;
use Aheadworks\Bup\Model\Source\UserProfile\Area;

/**
 * Class ListBuilder
 *
 * @package Aheadworks\Bup\Model\UserProfile
 */
class ListBuilder
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var UserProfileMetadataRepositoryInterface
     */
    private $userProfileMetadataRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param UserProfileMetadataRepositoryInterface $userProfileMetadataRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        UserProfileMetadataRepositoryInterface $userProfileMetadataRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->userProfileMetadataRepository = $userProfileMetadataRepository;
    }

    /**
     * Get profile list
     *
     * @param string $area
     * @return UserProfileMetadataInterface[]
     */
    public function getProfileList($area = Area::BACKEND)
    {
        try {
            $list = $this->userProfileMetadataRepository->getList($this->buildSearchCriteria(), $area);
            $result = $list->getItems();
        } catch (LocalizedException $e) {
            $result = [];
        }
        return $result;
    }

    /**
     * Get search criteria builder
     *
     * @return SearchCriteriaBuilder
     */
    public function getSearchCriteriaBuilder()
    {
        return $this->searchCriteriaBuilder;
    }

    /**
     * Build search criteria
     *
     * @return SearchCriteria
     */
    private function buildSearchCriteria()
    {
        $this->prepareSearchCriteriaBuilder();
        return $this->searchCriteriaBuilder->create();
    }

    /**
     * Prepares search criteria builder
     *
     * @return void
     */
    private function prepareSearchCriteriaBuilder()
    {
        $statusSortOrder = $this->sortOrderBuilder
            ->setField(UserProfileMetadataInterface::STATUS)
            ->setDescendingDirection()
            ->create();
        $this->searchCriteriaBuilder->addSortOrder($statusSortOrder);
    }
}
