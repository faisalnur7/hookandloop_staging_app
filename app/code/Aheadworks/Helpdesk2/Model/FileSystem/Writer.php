<?php
namespace Aheadworks\Helpdesk2\Model\FileSystem;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Writer
 *
 * @package Aheadworks\Helpdesk2\Model\FileSystem
 */
class Writer
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(
        Filesystem $filesystem
    ) {
        $this->filesystem = $filesystem;
    }

    /**
     * Set file content to temporary file
     *
     * @param array $file
     * @return array
     * @throws FileSystemException
     */
    public function saveToTemporaryFile($file)
    {
        $tmpDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::SYS_TMP);
        $tmpFileName = sha1(microtime()) . $file['filename'];
        $tmpDirectory->writeFile($tmpFileName, $file['content']);

        return [
            'tmp_name' => $tmpDirectory->getAbsolutePath() . $tmpFileName,
            'name' => $file['filename']
        ];
    }

    /**
     * Remove file from media folder if exists
     *
     * @param string $relativeDirectory
     * @param string $relativePath
     * @return bool
     * @throws FileSystemException
     */
    public function removeFileFromMedia($relativeDirectory, $relativePath)
    {
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $filePath = $relativeDirectory . DIRECTORY_SEPARATOR . ltrim((string)$relativePath, '/');
        if ($mediaDirectory->isFile($filePath)) {
            $mediaDirectory->delete($filePath);
        }

        return true;
    }
}
