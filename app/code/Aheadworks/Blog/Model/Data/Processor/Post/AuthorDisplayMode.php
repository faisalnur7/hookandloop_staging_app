<?php
namespace Aheadworks\Blog\Model\Data\Processor\Post;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;
use Aheadworks\Blog\Model\Source\Post\AuthorDisplayMode as SourceAuthorDisplayMode;

/**
 * Class AuthorDisplayMode
 */
class AuthorDisplayMode implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if (isset($data['use_default'])) {
            if (!empty($data['use_default'][PostInterface::AUTHOR_DISPLAY_MODE])) {
                $data[PostInterface::AUTHOR_DISPLAY_MODE] = SourceAuthorDisplayMode::USE_DEFAULT_OPTION;
            }
        }

        return $data;
    }
}