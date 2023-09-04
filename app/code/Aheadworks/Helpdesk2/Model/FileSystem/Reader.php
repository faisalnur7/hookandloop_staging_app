<?php
namespace Aheadworks\Helpdesk2\Model\FileSystem;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Reader
 *
 * @package Aheadworks\Helpdesk2\Model\FileSystem
 */
class Reader
{
    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var FileInfo
     */
    private $fileInfo;

    /**
     * @param Filesystem $filesystem
     * @param FileInfo $fileInfo
     */
    public function __construct(
        Filesystem $filesystem,
        FileInfo $fileInfo
    ) {
        $this->fileSystem = $filesystem;
        $this->fileInfo = $fileInfo;
    }

    /**
     * Create file for file factory
     *
     * @param string $filePath
     * @return array
     * @throws LocalizedException
     */
    public function createFile($filePath)
    {
        $directory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        $filePath = $this->fileInfo->getMediaFilePath($filePath);
        if (!$directory->isFile($filePath)) {
            throw new LocalizedException(__('File not found'));
        }

        return [
            'type' => 'filename',
            'value' => $filePath,
            'rm' => false
        ];
    }
}
