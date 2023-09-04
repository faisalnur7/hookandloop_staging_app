<?php
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface StorefrontLabelInterface
 * @api
 */
interface StorefrontLabelInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const STORE_ID = 'store_id';
    const CONTENT = 'content';
    /**#@-*/

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Get content
     *
     * @return string
     */
    public function getContent();

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content);

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\StorefrontLabelExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\StorefrontLabelExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\StorefrontLabelExtensionInterface $extensionAttributes
    );
}
