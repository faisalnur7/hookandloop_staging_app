<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action as BackendAction;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Collection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\CollectionFactory;
use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;

/**
 * Class AbstractMassAction
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway
 */
abstract class AbstractMassAction extends BackendAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::gateways';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var GatewayRepositoryInterface
     */
    protected $gatewayRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param GatewayRepositoryInterface $gatewayRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        GatewayRepositoryInterface $gatewayRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->gatewayRepository = $gatewayRepository;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            /** @var Collection $collection */
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            return $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('*/*/index');
        }
    }

    /**
     * Performs mass action
     *
     * @param Collection $collection
     * @return ResultInterface|ResponseInterface
     */
    abstract protected function massAction(Collection $collection);
}
