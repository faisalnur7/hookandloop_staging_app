<?php
namespace Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class Preview
 * @package Aheadworks\Blog\Block\Adminhtml\Post\Edit\Button
 */
class Preview implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $button = [
            'label' => __('Preview'),
            'class' => 'preview',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'preview'],
                ],
            ],
            'sort_order' => 45
        ];

        return $button;
    }
}
