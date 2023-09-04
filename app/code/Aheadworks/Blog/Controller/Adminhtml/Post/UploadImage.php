<?php
declare(strict_types=1);

namespace Aheadworks\Blog\Controller\Adminhtml\Post;

use Aheadworks\Blog\Controller\Adminhtml\Upload;

/**
 * Class UploadImage
 */
class UploadImage extends Upload
{
    /**
     * @var string
     */
    const FILE_ID = 'image_file';

    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Blog::posts';
}
