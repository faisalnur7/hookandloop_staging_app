<?php
namespace Aheadworks\Helpdesk2\Block\Adminhtml\Button;

use Magento\Backend\Block\Widget\Context;

/**
 * Class SaveAndContinue
 *
 * @package Aheadworks\Helpdesk2\Block\Adminhtml\Button
 */
class SaveAndContinue extends AbstractButton
{
    /**
     * @var int
     */
    private $sortOrder;

    /**
     * @param Context $context
     * @param int $sortOrder
     */
    public function __construct(
        Context $context,
        int $sortOrder = 30
    ) {
        parent::__construct($context);
        $this->sortOrder = $sortOrder;
    }

    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
            'sort_order' => $this->sortOrder,
        ];
    }
}
