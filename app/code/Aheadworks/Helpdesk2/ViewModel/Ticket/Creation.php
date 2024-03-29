<?php
namespace Aheadworks\Helpdesk2\ViewModel\Ticket;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\Department\Search\Builder as DepartmentSearch;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer\CreationRendererInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer\CreationRendererInterfaceFactory;

/**
 * Class Creation
 *
 * @package Aheadworks\Helpdesk2\ViewModel\Ticket
 */
class Creation implements ArgumentInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DepartmentSearch
     */
    private $departmentSearch;

    /**
     * @var CreationRendererInterfaceFactory
     */
    private $creationRendererFactory;

    /**
     * @param StoreManagerInterface $storeManager
     * @param DepartmentSearch $departmentSearch
     * @param CreationRendererInterfaceFactory $creationRendererFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        DepartmentSearch $departmentSearch,
        CreationRendererInterfaceFactory $creationRendererFactory
    ) {
        $this->storeManager = $storeManager;
        $this->departmentSearch = $departmentSearch;
        $this->creationRendererFactory = $creationRendererFactory;
    }

    /**
     * Get ticket creation renderer
     *
     * @return CreationRendererInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getTicketCreationRenderer()
    {
        /** @var CreationRendererInterface $renderer */
        $renderer = $this->creationRendererFactory->create();
        $storeId = $this->storeManager->getStore()->getId();

        $renderer
            ->setStoreId($storeId)
            ->setDepartments($this->getDepartments($storeId));

        return $renderer;
    }

    /**
     * Get departments
     *
     * @param int $storeId
     * @return DepartmentInterface[]
     * @throws LocalizedException
     */
    private function getDepartments($storeId)
    {
        $this->departmentSearch->addIsActiveFilter();
        $this->departmentSearch->addStoreFilter($storeId);
        $this->departmentSearch->addSortingBySortOrder();

        return $this->departmentSearch->searchDepartments($storeId);
    }
}
