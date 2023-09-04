<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Export\File;

use Aheadworks\Blog\Model\Directory;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\ImportExport\Controller\Adminhtml\Export as ExportController;
use Magento\Framework\Filesystem;
use Throwable;

/**
 * Class Download
 */
class Download extends ExportController implements HttpGetActionInterface
{
    /**
     * Url to this controller
     */
    const URL = 'aw_blog_admin/export_file/download/';

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * DownloadFile constructor.
     * @param Action\Context $context
     * @param FileFactory $fileFactory
     * @param Filesystem $filesystem
     */
    public function __construct(
        Action\Context $context,
        FileFactory $fileFactory,
        Filesystem $filesystem
    ) {
        $this->fileFactory = $fileFactory;
        $this->filesystem = $filesystem;
        parent::__construct($context);
    }

    /**
     * Controller basic method implementation.
     *
     * @return \Magento\Framework\Controller\Result\Redirect | \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        $fileName = $this->getRequest()->getParam('filename');
        $exportDirectory = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR);

        try {
            $fileExist = $exportDirectory->isExist(Directory::AW_BLOG_EXPORT . '/' . $fileName);
        } catch (Throwable $e) {
            $fileExist = false;
        }

        if (empty($fileName) || !$fileExist) {
            $this->messageManager->addErrorMessage(__('Please provide valid export file name'));

            return $resultRedirect;
        }

        try {
            $path = Directory::AW_BLOG_EXPORT . '/' . $fileName;
            $directory = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
            if ($directory->isFile($path)) {
                return $this->fileFactory->create(
                    $path,
                    $directory->readFile($path),
                    DirectoryList::VAR_DIR
                );
            }
            $this->messageManager->addErrorMessage(__('%1 is not a valid file', $fileName));
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect;
    }
}