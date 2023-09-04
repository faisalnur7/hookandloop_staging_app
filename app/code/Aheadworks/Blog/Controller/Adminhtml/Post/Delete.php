<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aheadworks\Blog\Api\PostRepositoryInterface;

/**
 * Class Delete
 * @package Aheadworks\Blog\Controller\Adminhtml\Post
 */
class Delete extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Blog::posts';

    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @param Context $context
     * @param PostRepositoryInterface $postRepository
     */
    public function __construct(
        Context $context,
        PostRepositoryInterface $postRepository
    ) {
        parent::__construct($context);
        $this->postRepository = $postRepository;
    }

    /**
     * Delete post action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $postId = (int)$this->getRequest()->getParam('id');
        if ($postId) {
            try {
                $this->postRepository->deleteById($postId);
                $this->messageManager->addSuccessMessage(__('Post was successfully deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }
        $this->messageManager->addErrorMessage(__('Post could not be deleted.'));
        return $resultRedirect->setPath('*/*/');
    }
}
