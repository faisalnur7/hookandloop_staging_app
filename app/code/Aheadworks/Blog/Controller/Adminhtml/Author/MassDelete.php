<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Author;

use Aheadworks\Blog\Model\ResourceModel\Author\Collection;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class MassDelete
 * @package Aheadworks\Blog\Controller\Adminhtml\Author
 */
class MassDelete extends AbstractMassAction
{
    /**
     * @inheritdoc
     */
    protected function massAction(Collection $collection)
    {
        $deletedRecords = 0;
        foreach ($collection->getAllIds() as $authorId) {
            try {
                $this->authorRepository->deleteById($authorId);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
            $deletedRecords++;
        }
        if ($deletedRecords) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were deleted.', $deletedRecords));
        } else {
            $this->messageManager->addSuccessMessage(__('No records were deleted.'));
        }
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/');
        return $resultRedirect;
    }
}
