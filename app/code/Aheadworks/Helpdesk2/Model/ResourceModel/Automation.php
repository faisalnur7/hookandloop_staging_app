<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel;

use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;

/**
 * Class Automation
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel
 */
class Automation extends AbstractResourceModel
{
    const MAIN_TABLE_NAME = 'aw_helpdesk2_automation';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, AutomationInterface::ID);
    }
}
