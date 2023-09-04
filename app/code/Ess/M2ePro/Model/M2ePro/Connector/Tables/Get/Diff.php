<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\M2ePro\Connector\Tables\Get;

/**
 * Class \Ess\M2ePro\Model\M2ePro\Connector\Tables\Get\Diff
 */
class Diff extends \Ess\M2ePro\Model\Connector\Command\RealTime
{
    public const SEVERITY_CRITICAL = 'critical';
    public const SEVERITY_WARNING = 'warning';
    public const SEVERITY_NOTICE = 'notice';

    //########################################

    protected function getCommand()
    {
        return ['tables', 'get', 'diff'];
    }

    protected function getRequestData()
    {
        return [
            'tables_info' => \Ess\M2ePro\Helper\Json::encode(
                $this->getHelper('Module_Database_Structure')->getModuleTablesInfo()
            ),
        ];
    }

    protected function validateResponse()
    {
        return true;
    }

    //########################################
}
