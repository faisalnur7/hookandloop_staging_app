<?php
namespace Aheadworks\BlogSearch\Ui\DataProvider\ItemModifier\Post;

use Aheadworks\Blog\Api\Data\AuthorInterface;
use Aheadworks\Blog\Api\Data\AuthorInterfaceFactory;
use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\BlogSearch\Ui\DataProvider\ItemModifier\ModifierInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class Author
 */
class Author implements ModifierInterface
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var AuthorInterfaceFactory
     */
    private $authorFactory;

    /**
     * Author constructor.
     * @param DataObjectHelper $dataObjectHelper
     * @param AuthorInterfaceFactory $authorFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        AuthorInterfaceFactory $authorFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->authorFactory = $authorFactory;
    }

    /**
     * @inheritdoc
     * @param PostInterface $item
     * @returns PostInterface
     */
    public function modify($item)
    {
        $postAuthor = $item->getAuthor();

        if (is_array($postAuthor) && !empty($postAuthor)) {
            $authorObject = $this->authorFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $authorObject,
                $postAuthor,
                AuthorInterface::class
            );
            $item->setAuthor($authorObject);
        }

        return $item;
    }
}
