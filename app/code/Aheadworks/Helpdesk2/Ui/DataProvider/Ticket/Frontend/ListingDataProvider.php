<?php
namespace Aheadworks\Helpdesk2\Ui\DataProvider\Ticket\Frontend;

use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\Config\Share as ShareConfig;
use Magento\Store\Api\StoreWebsiteRelationInterface as StoreResolver;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;

/**
 * Class ListingDataProvider
 *
 * @package Aheadworks\Helpdesk2\Ui\DataProvider\Ticket\Frontend
 */
class ListingDataProvider extends DataProvider
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var ShareConfig
     */
    private $shareConfig;

    /**
     * @var StoreResolver
     */
    private $storeResolver;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param CustomerSession $customerSession
     * @param ShareConfig $shareConfig
     * @param StoreResolver $storeResolver
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        CustomerSession $customerSession,
        ShareConfig $shareConfig,
        StoreResolver $storeResolver,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->customerSession = $customerSession;
        $this->shareConfig = $shareConfig;
        $this->storeResolver = $storeResolver;
    }

    /**
     * @inheritdoc
     */
    public function getSearchCriteria()
    {
        $customer = $this->customerSession->getCustomer();
        if ($customer->getId()) {
            $this->addCustomerFilter($customer);
        } else {
            $this->addEmptyFilter();
        }
        return parent::getSearchCriteria();
    }

    /**
     * Add customer filter
     *
     * @param CustomerInterface|Customer $customer
     */
    private function addCustomerFilter($customer)
    {
        $filter = $this->filterBuilder
            ->setField('main_table.' . TicketInterface::CUSTOMER_ID)
            ->setValue($customer->getId())
            ->setConditionType('eq')->create();
        $this->addFilter($filter);

        if (!$this->shareConfig->isGlobalScope()) {
            $filter = $this->filterBuilder
                ->setField(Ticket::TICKET_ENTITY_TABLE_NAME . '.' . TicketInterface::STORE_ID)
                ->setValue($this->storeResolver->getStoreByWebsiteId($customer->getWebsiteId()))
                ->setConditionType('in')->create();
            $this->addFilter($filter);
        }

    }

    /**
     * Add empty filter
     */
    private function addEmptyFilter()
    {
        $filter = $this->filterBuilder
            ->setField(TicketInterface::CUSTOMER_ID)
            ->setValue(0)
            ->setConditionType('eq')->create();
        $this->addFilter($filter);
    }
}
