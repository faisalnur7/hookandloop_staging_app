<?php
namespace Aheadworks\Helpdesk2\Block\Adminhtml\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;

/**
 * Class AbstractButton
 *
 * @package Aheadworks\Helpdesk2\Block\Adminhtml\Button
 */
abstract class AbstractButton implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    protected function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
