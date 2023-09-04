<?php
namespace Aheadworks\Helpdesk2\Block\Adminhtml\Rejecting\Pattern\Form\Button;

use Aheadworks\Helpdesk2\Block\Adminhtml\Button\AbstractButton;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;

/**
 * Class Delete
 *
 * @package Aheadworks\Helpdesk2\Block\Adminhtml\Rejecting\Pattern\Form\Button.
 */
class Delete extends AbstractButton
{
    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        $data = [];
        $patternId = $this->context->getRequest()->getParam(RejectingPatternInterface::ID);
        if ($patternId) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getUrl('*/*/delete', [RejectingPatternInterface::ID => $patternId]) . '\')',
                'sort_order' => 15,
            ];
        }

        return $data;
    }
}
