<?php
namespace Aheadworks\Helpdesk2\Block\Adminhtml\QuickResponse\Form\Button;

use Aheadworks\Helpdesk2\Block\Adminhtml\Button\AbstractButton;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;

/**
 * Class Delete
 *
 * @package Aheadworks\Helpdesk2\Block\Adminhtml\QuickResponse\Form\Button
 */
class Delete extends AbstractButton
{
    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        $data = [];
        $quickResponseId = $this->context->getRequest()->getParam(QuickResponseInterface::ID);
        if ($quickResponseId) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getUrl('*/*/delete', [QuickResponseInterface::ID => $quickResponseId]) . '\')',
                'sort_order' => 15,
            ];
        }

        return $data;
    }
}
