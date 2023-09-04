<?php
namespace Aheadworks\Bup\Model\DataProcessor\UserProfilePostDataProcessor;

use Aheadworks\Bup\Model\DataProcessor\PostDataProcessorInterface;
use Aheadworks\Bup\Model\UserProfile\ImageUploader;
use Aheadworks\Bup\Api\Data\UserProfileInterface;
use Aheadworks\Bup\Block\Adminhtml\User\Edit\Tab\FormElementApplier;

/**
 * Class Image
 *
 * @package Aheadworks\Bup\Model\DataProcessor\UserProfilePostDataProcessor
 */
class Image implements PostDataProcessorInterface
{
    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        ImageUploader $imageUploader
    ) {
        $this->imageUploader = $imageUploader;
    }

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        $result = $this->imageUploader->saveImageToMediaFolder(FormElementApplier::IMAGE_INPUT_NAME);
        if (isset($result['file'])) {
            $data[UserProfileInterface::IMAGE] = $result['file'];
        }
        if (isset($data[UserProfileInterface::IMAGE . '_delete'])) {
            $data[UserProfileInterface::IMAGE] = '';
        }

        return $data;
    }
}
