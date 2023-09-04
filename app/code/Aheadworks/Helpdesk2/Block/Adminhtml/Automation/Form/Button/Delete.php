<?php
namespace Aheadworks\Helpdesk2\Block\Adminhtml\Automation\Form\Button;

use Aheadworks\Helpdesk2\Block\Adminhtml\Button\AbstractButton;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;

/**
 * Class Delete
 *
 * @package Aheadworks\Helpdesk2\Block\Adminhtml\Automation\Form\Button
 */
class Delete extends AbstractButton
{
    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        $data = [];
        $automationId = $this->context->getRequest()->getParam(AutomationInterface::ID);
        if ($automationId) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getUrl('*/*/delete', [AutomationInterface::ID => $automationId]) . '\')',
                'sort_order' => 15,
            ];
        }

        return $data;
    }
}
