<?php
namespace Aheadworks\Helpdesk2\Block\Adminhtml\Gateway\Form\Button;

use Aheadworks\Helpdesk2\Block\Adminhtml\Button\AbstractButton;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;

/**
 * Class Delete
 *
 * @package Aheadworks\Helpdesk2\Block\Adminhtml\Gateway\Form\Button
 */
class Delete extends AbstractButton
{
    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        $data = [];
        $gatewayId = $this->context->getRequest()->getParam(GatewayDataInterface::ID);
        if ($gatewayId) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getUrl('*/*/delete', [GatewayDataInterface::ID => $gatewayId]) . '\')',
                'sort_order' => 15,
            ];
        }

        return $data;
    }
}
