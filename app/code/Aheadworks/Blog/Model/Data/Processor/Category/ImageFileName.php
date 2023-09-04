<?php
namespace Aheadworks\Blog\Model\Data\Processor\Category;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;

/**
 * Class ImageFileName
 *
 * @package Aheadworks\Blog\Model\Data\Processor\Category
 */
class ImageFileName implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        $imageFileName = isset($data[CategoryInterface::IMAGE_FILE_NAME][0]['file'])
            ? $data[CategoryInterface::IMAGE_FILE_NAME][0]['file']
            : '';
        $data[CategoryInterface::IMAGE_FILE_NAME] = $imageFileName;
        return $data;
    }
}
