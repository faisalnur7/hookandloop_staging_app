<?php
namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Attachment;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\Data\EmailAttachmentInterface;
use Aheadworks\Helpdesk2\Api\Data\EmailAttachmentInterfaceFactory;
use Aheadworks\Helpdesk2\Model\FileSystem\Writer as FileSystemWriter;
use Aheadworks\Helpdesk2\Model\FileSystem\FileUploader;
use Magento\Framework\Validation\ValidationException;

/**
 * Class Factory
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Attachment
 */
class Factory
{
    /**
     * @var EmailAttachmentInterfaceFactory
     */
    private $attachmentFactory;

    /**
     * @var FileSystemWriter
     */
    private $fileSystemWriter;

    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * @param EmailAttachmentInterfaceFactory $attachmentFactory
     * @param FileSystemWriter $fileSystemWriter
     * @param FileUploader $fileUploader
     */
    public function __construct(
        EmailAttachmentInterfaceFactory $attachmentFactory,
        FileSystemWriter $fileSystemWriter,
        FileUploader $fileUploader
    ) {
        $this->attachmentFactory = $attachmentFactory;
        $this->fileSystemWriter = $fileSystemWriter;
        $this->fileUploader = $fileUploader;
    }

    /**
     * Create attachment
     *
     * @param array $attachmentData
     * @return EmailAttachmentInterface
     * @throws FileSystemException
     * @throws LocalizedException
     * @throws ValidationException
     */
    public function create($attachmentData)
    {
        $tmpFile = $this->fileSystemWriter->saveToTemporaryFile($attachmentData);
        $result = $this->fileUploader->upload($tmpFile);
        if (isset($result['exception']) && $result['exception'] instanceof ValidationException) {
            throw $result['exception'];
        } else if (isset($result['error'])) {
            throw new LocalizedException(__('Error while saving attachment: %1', $result['error']));
        }

        /** @var EmailAttachmentInterface $attachment */
        $attachment = $this->attachmentFactory->create();
        $attachment
            ->setFilePath($result['file'])
            ->setFileName($result['name']);

        return $attachment;
    }
}
