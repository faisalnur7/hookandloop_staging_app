<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel;

use Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface;

/**
 * Class RejectedMessage
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel
 */
class RejectedMessage extends AbstractResourceModel
{
    const MAIN_TABLE_NAME = 'aw_helpdesk2_rejected_message';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, RejectedMessageInterface::ID);
    }
}
