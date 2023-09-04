<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel;

use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractResourceModel;

/**
 * Class RejectingPattern
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email
 */
class RejectingPattern extends AbstractResourceModel
{
    /**#@+
     * Constants defined for table names
     */
    const MAIN_TABLE_NAME = 'aw_helpdesk2_email_rejecting_pattern';
    const SCOPE_TABLE = 'aw_helpdesk2_email_rejecting_pattern_scope';
    /**#@-*/

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, RejectingPatternInterface::ID);
    }
}
