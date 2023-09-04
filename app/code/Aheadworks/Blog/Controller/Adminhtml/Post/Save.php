<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aheadworks\Blog\Api\Data\PostInterface;
use Magento\Framework\Message\Error;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface as DataProcessorInterface;
use Aheadworks\Blog\Api\PostRepositoryInterface;
use Aheadworks\Blog\Api\Data\PostInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class Save
 * @package Aheadworks\Blog\Controller\Adminhtml\Post
 */
class Save extends Action
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
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var PostInterfaceFactory
     */
    private $postDataFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @param Context $context
     * @param DataProcessorInterface $postDataProcessor
     * @param PostRepositoryInterface $postRepository
     * @param PostInterfaceFactory $postDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        DataProcessorInterface $postDataProcessor,
        PostRepositoryInterface $postRepository,
        PostInterfaceFactory $postDataFactory,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);

        $this->postDataProcessor = $postDataProcessor;
        $this->postRepository = $postRepository;
        $this->postDataFactory = $postDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * Save post action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($postData = $this->getRequest()->getPostValue()) {
            $postData = $this->postDataProcessor->process($postData);
            $postId = isset($postData['id']) ? $postData['id'] : false;
            try {
                $postDataObject = $postId
                    ? $this->postRepository->get($postId)
                    : $this->postDataFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $postDataObject,
                    $postData,
                    PostInterface::class
                );
                /** @var PostInterface $post */
                $post = $this->postRepository->save($postDataObject);
                $this->dataPersistor->clear('aw_blog_post');
                $this->messageManager->addSuccessMessage(__('The post was successfully saved.'));
                $back = $this->getRequest()->getParam('back');
                if ($back == 'edit') {
                    return $resultRedirect->setPath(
                        '*/*/' . $back,
                        [
                            'id' => $post->getId(),
                            '_current' => true
                        ]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Validator\Exception $exception) {
                $messages = $exception->getMessages();
                if (empty($messages)) {
                    $messages = [$exception->getMessage()];
                }
                foreach ($messages as $message) {
                    if (!$message instanceof Error) {
                        $message = new Error($message);
                    }
                    $this->messageManager->addMessage($message);
                }
            } catch (LocalizedException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while saving the post.')
                );
            }
            unset($postData[PostInterface::PRODUCT_CONDITION]);
            $this->dataPersistor->set('aw_blog_post', $postData);
            if ($postId) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $postId, '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/new', ['_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
