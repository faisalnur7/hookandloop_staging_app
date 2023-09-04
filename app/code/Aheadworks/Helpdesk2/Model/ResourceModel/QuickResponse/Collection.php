<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\QuickResponse;

use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\StorefrontLabel\AbstractCollection;
use Aheadworks\Helpdesk2\Model\QuickResponse as QuickResponseModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\QuickResponse as QuickResponseResourceModel;

/**
 * Class Collection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\QuickResponse
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = QuickResponseInterface::ID;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(QuickResponseModel::class, QuickResponseResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    protected function getStorefrontLabelEntityType()
    {
        return QuickResponseInterface::STOREFRONT_LABEL_ENTITY_TYPE;
    }
}
