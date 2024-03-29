<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Controller\Adminhtml\ControlPanel;

use Ess\M2ePro\Helper\Module;
use Magento\Backend\App\Action;

/**
 * Class \Ess\M2ePro\Controller\Adminhtml\ControlPanel\InspectionTab
 */
class InspectionTab extends Main
{
    public function execute()
    {
        $block = $this->getLayout()->createBlock(\Ess\M2ePro\Block\Adminhtml\ControlPanel\Tabs\Inspection::class, '');
        $this->setAjaxContent($block);

        return $this->getResult();
    }
}
