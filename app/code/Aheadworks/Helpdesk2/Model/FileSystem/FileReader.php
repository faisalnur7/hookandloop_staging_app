<?php
namespace Aheadworks\Helpdesk2\Model\FileSystem;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * Class FileReader
 *
 * @package Aheadworks\Helpdesk2\Model\FileSystem
 */
class FileReader
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
     * Open file and read its content
     *
     * @param string|array $content set to null to avoid starting output,
     * $contentLength should be set explicitly in that case
     * @param string $baseDir
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @return string|bool false in case of any error occurs
     */
    public function read(
        $content,
        $baseDir = DirectoryList::ROOT
    ) {
        $dir = $this->filesystem->getDirectoryWrite($baseDir);
        $file = null;
        if (!isset($content['type']) || !isset($content['value'])) {
            throw new \InvalidArgumentException("Invalid arguments. Keys 'type' and 'value' are required.");
        }
        if ($content['type'] == 'filename') {
            $file = $content['value'];
            if (!$dir->isFile($file)) {
                return false;
            }
        } else {
            return false;
        }

        $stream = $dir->openFile($file, 'r');
        $fileContent = '';
        while (!$stream->eof()) {
            $fileContent .= $stream->read(1024);
        }
        $stream->close();
        flush();

        if (!empty($content['rm'])) {
            $dir->delete($file);
        }

        return $fileContent;
    }
}
