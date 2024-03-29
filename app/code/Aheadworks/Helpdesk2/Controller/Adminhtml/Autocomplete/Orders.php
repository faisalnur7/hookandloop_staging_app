<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Autocomplete;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\CustomerOrders;
use Aheadworks\Helpdesk2\Model\Source\Ticket\CustomerOrdersFactory;

/**
 * Class Orders
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Autocomplete
 */
class Orders extends Action
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
     * @var CustomerOrdersFactory
     */
    private $customerOrdersFactory;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerOrdersFactory $customerOrdersFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CustomerRepositoryInterface $customerRepository,
        CustomerOrdersFactory $customerOrdersFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerRepository = $customerRepository;
        $this->customerOrdersFactory = $customerOrdersFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $customerEmail = $this->getRequest()->getParam('customer_email');
        try {
            $customer = $this->customerRepository->get($customerEmail);
            /** @var CustomerOrders $optionSource */
            $optionSource = $this->customerOrdersFactory->create(['customerId' => $customer->getId()]);
            $result = [
                'error' => false,
                'options' => $optionSource->toOptionArray()
            ];
        } catch (\Exception $exception) {
            $result = [
                'error' => true,
                'message' => __($exception->getMessage())
            ];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
