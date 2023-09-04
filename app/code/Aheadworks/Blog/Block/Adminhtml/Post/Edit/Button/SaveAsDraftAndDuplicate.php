<?php
namespace Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveAsDraftAndDuplicate
 */
class SaveAsDraftAndDuplicate implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save as draft & Duplicate'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAsDraftAndDuplicate'],
                ],
            ],
            'sort_order' => 35,
        ];
    }
}
