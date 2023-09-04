<?php
namespace Aheadworks\Helpdesk2\Model\FileSystem;

use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\MediaStorage\Model\File\Uploader;

/**
 * Class FileUploader
 *
 * @package Aheadworks\Helpdesk2\Model\FileSystem
 */
class FileUploader
{
    /**
     * @var FileInfo
     */
    private $fileInfo;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @param FileInfo $fileInfo
     * @param UploaderFactory $uploaderFactory
     */
    public function __construct(
        FileInfo $fileInfo,
        UploaderFactory $uploaderFactory
    ) {
        $this->fileInfo = $fileInfo;
        $this->uploaderFactory = $uploaderFactory;
    }

    /**
     * @inheritdoc
     */
    public function upload($file)
    {
        try {
            $result = ['file' => '', 'size' => '', 'name' => '', 'path' => '', 'type' => ''];
            $mediaDirectory = $this->fileInfo->getMediaDirectory()->getAbsolutePath(FileInfo::FILE_DIR);

            /** @var Uploader $uploader */
            $uploader = $this->uploaderFactory->create(['fileId' => $file]);
            $uploader
                ->setAllowRenameFiles(true)
                ->setFilesDispersion(true);

            $result = array_intersect_key($uploader->save($mediaDirectory), $result);
            $result['url'] = $this->fileInfo->getMediaUrl($result['file']);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode(), 'exception' => $e];
        }

        return $result;
    }
}
