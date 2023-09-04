<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Category;

use Aheadworks\Blog\Controller\Adminhtml\Upload;

/**
 * Class UploadImage
 *
 * @package Aheadworks\Blog\Controller\Adminhtml\Category
 */
class UploadImage extends Upload
{
    /**
     * @var string
     */
    const FILE_ID = 'image_file_name';

    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Blog::categories';
}
