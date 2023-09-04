<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel;

use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;

/**
 * Class QuickResponse
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel
 */
class QuickResponse extends AbstractResourceModel
{
    const MAIN_TABLE_NAME = 'aw_helpdesk2_quick_response';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, QuickResponseInterface::ID);
    }
}
