<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aheadworks\Blog\Api\Data\PostInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\Error;
use Magento\Framework\Message\MessageInterface;
use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface as DataProcessorInterface;
use Aheadworks\Blog\Api\Data\PostInterfaceFactory;
use Aheadworks\Blog\Model\PostFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Validate
 * @package Aheadworks\Blog\Controller\Adminhtml\Post
 */
class Validate extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Blog::posts';

    /**
     * @var DataProcessorInterface
     */
    private $postDataProcessor;

    /**
     * @var PostInterfaceFactory
     */
    private $postDataFactory;

    /**
     * @var PostFactory
     */
    private $postFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @param Context $context
     * @param DataProcessorInterface $postDataProcessor
     * @param PostInterfaceFactory $postDataFactory
     * @param PostFactory $postFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        DataProcessorInterface $postDataProcessor,
        PostInterfaceFactory $postDataFactory,
        PostFactory $postFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);

        $this->postDataProcessor = $postDataProcessor;
        $this->postDataFactory = $postDataFactory;
        $this->postFactory = $postFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Validate post
     *
     * @param array $response
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function validate($response)
    {
        $errors = [];
        if ($postData = $this->getRequest()->getPostValue()) {
            try {
                /** @var PostInterface $postDataObject */
                $postDataObject = $this->postDataFactory->create();
                $postData = $this->postDataProcessor->process($postData);
                $this->dataObjectHelper->populateWithArray(
                    $postDataObject,
                    $postData,
                    PostInterface::class
                );
                /** @var \Aheadworks\Blog\Model\Post $postModel */
                $postModel = $this->postFactory->create();
                $postModel->setData(
                    $this->dataObjectProcessor->buildOutputDataArray(
                        $postDataObject,
                        PostInterface::class
                    )
                );
                $postModel->validateBeforeSave();
            } catch (\Magento\Framework\Validator\Exception $exception) {
                /* @var $error Error */
                foreach ($exception->getMessages(MessageInterface::TYPE_ERROR) as $error) {
                    $errors[] = $error->getText();
                }
            } catch (LocalizedException $exception) {
                $errors[] = $exception->getMessage();
            }
        }
        if ($errors) {
            $messages = $response->hasMessages() ? $response->getMessages() : [];
            foreach ($errors as $error) {
                $messages[] = $error;
            }
            $response->setMessages($messages);
            $response->setError(1);
        }
    }

    /**
     * AJAX post validate action
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(0);

        $this->validate($response);
        $resultJson = $this->resultJsonFactory->create();
        if ($response->getError()) {
            $response->setError(true);
            $response->setMessages($response->getMessages());
        }

        $resultJson->setData($response);
        return $resultJson;
    }
}
