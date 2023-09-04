<?php
namespace Aheadworks\Blog\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface PostPreviewInterface
 * @package Aheadworks\Blog\Api\Data
 */
interface PostPreviewInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const POST_PREVIEW_DATA = 'post_preview_data';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get post preview data
     *
     * @return string
     */
    public function getPostPreviewData();

    /**
     * Set post preview data
     *
     * @param string $data
     * @return $this
     */
    public function setPostPreviewData($data);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Blog\Api\Data\PostPreviewExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Blog\Api\Data\PostPreviewExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Aheadworks\Blog\Api\Data\PostPreviewExtensionInterface $extensionAttributes);
}
