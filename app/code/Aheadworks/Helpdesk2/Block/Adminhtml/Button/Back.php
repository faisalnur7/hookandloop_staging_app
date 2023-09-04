<?php
namespace Aheadworks\Helpdesk2\Block\Adminhtml\Button;

use Magento\Backend\Block\Widget\Context;

/**
 * Class Back
 *
 * @package Aheadworks\Helpdesk2\Block\Adminhtml\Button
 */
class Back extends AbstractButton
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
        int $sortOrder = 10
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
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('*/*/')),
            'class' => 'back',
            'sort_order' => $this->sortOrder
        ];
    }
}
