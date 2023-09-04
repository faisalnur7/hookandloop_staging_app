<?php
namespace Aheadworks\Helpdesk2\Block\Adminhtml\Button;

use Magento\Backend\Block\Widget\Context;

/**
 * Class Save
 *
 * @package Aheadworks\Helpdesk2\Block\Adminhtml\Button
 */
class Save extends AbstractButton
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
        int $sortOrder = 40
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
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => $this->sortOrder,
        ];
    }
}
