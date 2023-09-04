<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket\Attachment;

use Aheadworks\Helpdesk2\Model\FileSystem\FileUploader;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Upload
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket\Attachment
 */
class Upload extends Action
{
    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * @param Context $context
     * @param FileUploader $fileUploader
     */
    public function __construct(
        Context $context,
        FileUploader $fileUploader
    ) {
        parent::__construct($context);
        $this->fileUploader = $fileUploader;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $imageId = $this->getRequest()->getParam('param_name', 'attachments');
            $result = $this->fileUploader->upload($imageId);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
