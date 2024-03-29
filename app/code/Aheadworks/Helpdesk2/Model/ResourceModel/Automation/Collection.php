<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Automation;

use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractCollection;
use Aheadworks\Helpdesk2\Model\Automation as AutomationModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation as AutomationResourceModel;

/**
 * Class Collection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Automation
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = AutomationInterface::ID;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(AutomationModel::class, AutomationResourceModel::class);
    }
}
