<?php
namespace Aheadworks\Helpdesk2\Block\Adminhtml\Button;

use Magento\Backend\Block\Widget\Context;

/**
 * Class Reset
 *
 * @package Aheadworks\Helpdesk2\Block\Adminhtml\Button
 */
class Reset extends AbstractButton
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
        int $sortOrder = 20
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
            'label' => __('Reset'),
            'class' => 'reset',
            'on_click' => 'location.reload();',
            'sort_order' => $this->sortOrder
        ];
    }
}
