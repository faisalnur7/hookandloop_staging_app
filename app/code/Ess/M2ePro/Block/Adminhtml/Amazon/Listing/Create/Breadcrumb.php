<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Block\Adminhtml\Amazon\Listing\Create;

/**
 * Class \Ess\M2ePro\Block\Adminhtml\Amazon\Listing\Create\Breadcrumb
 */
class Breadcrumb extends \Ess\M2ePro\Block\Adminhtml\Widget\Breadcrumb
{
    //########################################

    public function _construct()
    {
        parent::_construct();

        $this->setId('amazonListingBreadcrumb');

        $this->setSteps([
            [
                'id' => 1,
                'title' => __('Step 1'),
                'description' => __('General Settings'),
            ],
            [
                'id' => 2,
                'title' => __('Step 2'),
                'description' => __('Selling Settings'),
            ],
        ]);

        $this->setContainerData([
            'style' => 'margin-bottom: 30px;',
        ]);
    }

    //########################################
}
