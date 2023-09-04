<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Autocomplete;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Customers
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Autocomplete
 */
class Customers extends Action
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * Constructor
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CustomerRepositoryInterface $customers
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchBuilder
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CustomerRepositoryInterface $customers,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchBuilder
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerRepository = $customers;
        $this->searchBuilder = $searchBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $query = $this->getRequest()->getParam('query', '');
        $result = [
            'query'  => $query,
            'suggestions' => [],
        ];

        $filter = $this->filterBuilder
            ->setField(CustomerInterface::EMAIL)
            ->setConditionType('like')
            ->setValue('%' . $query . '%')
            ->create();
        $criteria = $this->searchBuilder
            ->addFilter($filter)
            ->setCurrentPage($this->getRequest()->getParam('page', 1))
            ->setPageSize(30)
            ->create();

        $searchResults = $this->customerRepository->getList($criteria);
        foreach ($searchResults->getItems() as $item) {
            $result['suggestions'][] = [
                'value' => $item->getEmail(),
                'customer_name' => $item->getFirstname() . ' ' . $item->getLastname()
            ];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
