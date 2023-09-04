<?php
namespace Aheadworks\Bup\Model\UserProfile;

use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\MediaStorage\Model\File\Uploader;

/**
 * Class ImageUploader
 *
 * @package Aheadworks\Bup\Model\UserProfile
 */
class ImageUploader
{
    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var ImageInfo
     */
    private $imageInfo;

    /**
     * @param UploaderFactory $uploaderFactory
     * @param ImageInfo $imageInfo
     */
    public function __construct(
        UploaderFactory $uploaderFactory,
        ImageInfo $imageInfo
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->imageInfo = $imageInfo;
    }

    /**
     * Save image to media directory
     *
     * @param string|array $file
     * @return $this
     */
    public function saveImageToMediaFolder($file)
    {
        try {
            $result = ['file' => ''];
            $mediaDirectory = $this->imageInfo->getMediaDirectory()->getAbsolutePath(ImageInfo::FILE_DIR);

            /** @var Uploader $uploader */
            $uploader = $this->uploaderFactory->create(['fileId' => $file]);
            $uploader->setFilesDispersion(false);
            $uploader->setAllowedExtensions($this->getAllowedExtensions());
            $result = array_intersect_key($uploader->save($mediaDirectory), $result);

            $result['url'] = $this->imageInfo->getMediaUrl($result['file']);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $result;
    }

    /**
     * Get allowed file extensions
     *
     * @return array
     */
    public function getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png'];
    }
}
