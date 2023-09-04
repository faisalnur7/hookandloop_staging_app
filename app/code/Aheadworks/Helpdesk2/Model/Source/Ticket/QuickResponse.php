<?php
namespace Aheadworks\Helpdesk2\Model\Source\Ticket;

use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;
use Aheadworks\Helpdesk2\Api\QuickResponseRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class QuickResponse
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Ticket
 */
class QuickResponse implements OptionSourceInterface
{
    const EMPTY_VALUE = '';

    /**
     * @var QuickResponseRepositoryInterface
     */
    private $quickResponseRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var array
     */
    private $options;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @param QuickResponseRepositoryInterface $quickResponseRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param TicketRepositoryInterface $ticketRepository
     * @param RequestInterface $request
     */
    public function __construct(
        QuickResponseRepositoryInterface $quickResponseRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        TicketRepositoryInterface $ticketRepository,
        RequestInterface $request
    ) {
        $this->quickResponseRepository = $quickResponseRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->ticketRepository = $ticketRepository;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function toOptionArray()
    {
        if (null === $this->options) {
            $this->options[] = [
                'value' => self::EMPTY_VALUE,
                'label' => __('Please select quick response...'),
                'response' => null
            ];

            $this->searchCriteriaBuilder
                ->addFilter(QuickResponseInterface::IS_ACTIVE, true)
                ->addSortOrder(
                    $this->sortOrderBuilder
                        ->setField(QuickResponseInterface::SORT_ORDER)
                        ->setAscendingDirection()
                        ->create()
                );
            $storeId = null;
            $ticketId = (int)$this->request->getParam(TicketInterface::ENTITY_ID);
            if ($ticketId) {
                $storeId = $this->ticketRepository->getById($ticketId)->getStoreId();
            }
            $responses = $this->quickResponseRepository->getList($this->searchCriteriaBuilder->create(), $storeId)->getItems();
            foreach ($responses as $response) {
                $this->options[] = [
                    'value' => $response->getId(),
                    'label' => $response->getTitle(),
                    'response' => nl2br($response->getCurrentStorefrontLabel()->getContent())
                ];
            }
        }

        return $this->options;
    }
}
