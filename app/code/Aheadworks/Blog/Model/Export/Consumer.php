<?php
namespace Aheadworks\Blog\Model\Export;

use Aheadworks\Blog\Model\Directory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Notification\NotifierInterface;
use Magento\ImportExport\Api\Data\ExportInfoInterface;
use Magento\ImportExport\Api\ExportManagementInterface;

/**
 * Class Consumer
 */
class Consumer
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var ExportManagementInterface
     */
    private $exportManager;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var NotifierInterface
     */
    private $notifier;

    /**
     * Consumer constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param ExportManagementInterface $exportManager
     * @param Filesystem $filesystem
     * @param NotifierInterface $notifier
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        ExportManagementInterface $exportManager,
        Filesystem $filesystem,
        NotifierInterface $notifier
    ) {
        $this->logger = $logger;
        $this->exportManager = $exportManager;
        $this->filesystem = $filesystem;
        $this->notifier = $notifier;
    }

    /**
     * @param ExportInfoInterface $exportInfo
     */
    public function process(ExportInfoInterface $exportInfo)
    {
        try {
            $data = $this->exportManager->export($exportInfo);
            $fileName = $exportInfo->getFileName();
            $directory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
            $directory->writeFile(Directory::AW_BLOG_EXPORT . '/' . $fileName, $data);

            $this->notifier->addMajor(
                __('Your export file is ready'),
                __('You can pick up your file at export main page')
            );
        } catch (FileSystemException $exception) {
            $this->notifier->addCritical(
                __('Error during export process occurred'),
                __('Error during export process occurred. Please check logs for detail')
            );
            $this->logger->critical('Something went wrong while export process. ' . $exception->getMessage());
        }
    }
}
