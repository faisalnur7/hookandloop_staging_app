<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Export;

use Aheadworks\Blog\Model\Export\Data\OperationInterface as DataOperationInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\ImportExport\Model\Export as ExportModel;
use Magento\ImportExport\Controller\Adminhtml\Export as ExportController;

/**
 * Class Export
 */
class Export extends ExportController
{
    /**
     * @var PublisherInterface
     */
    private $messagePublisher;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var DataOperationInterface
     */
    private $dataOperation;

    /**
     * Export constructor.
     * @param Context $context
     * @param PublisherInterface $messagePublisher
     * @param JsonFactory $resultJsonFactory
     * @param DataOperationInterface $dataOperation
     */
    public function __construct(
        Context $context,
        PublisherInterface $messagePublisher,
        JsonFactory $resultJsonFactory,
        DataOperationInterface $dataOperation
    ) {
        $this->messagePublisher = $messagePublisher;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataOperation = $dataOperation;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $messages = [];

        if ($this->getRequest()->getPost(ExportModel::FILTER_ELEMENT_GROUP)) {
            try {
                $params = $this->getRequest()->getParams();
                $dataObject = $this->dataOperation->execute($params);
                $this->messagePublisher->publish('aw_blog_import_export.export', $dataObject);

                $messages['success'] = __(
                    'Message is added to queue, wait to get your file soon.'
                    . ' Make sure your cron job is running to export the file'
                );
            } catch (\Exception $e) {
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $messages['error'] = __('Please correct the data sent value.');
            }
        } else {
            $messages['error'] = __('Please correct the data sent value.');
        }

        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData($messages);
    }
}