<?php
namespace Aheadworks\Blog\Model\Widget;

use Magento\Framework\View\Layout;
use Magento\Framework\View\Element\BlockInterface;

/**
 * Class HtmlProcessor
 */
class HtmlProcessor
{
    /**
     * @var Layout
     */
    private $layout;

    /**
     * @var RequestDataProvider
     */
    private $requestDataProvider;

    /**
     * HtmlProcessor constructor.
     * @param Layout $layout
     * @param RequestDataProvider $requestDataProvider
     */
    public function __construct(
        Layout $layout,
        RequestDataProvider $requestDataProvider
    ) {
        $this->layout = $layout;
        $this->requestDataProvider = $requestDataProvider;
    }

    /**
     * Execute Html Processor
     *
     * @param BlockInterface $block
     * @param array $data
     */
    public function execute($block, $data = []) {
        $html = '';

        if ($block) {
            $this->layout->addBlock($block);
            $block->addData($data);
            $html = $block->toHtml();

            if ($this->isNeedRenderSerializer()) {
                $html .= $this->renderSerializer($block);
            }
        }

        return $html;
    }

    /**
     * Check Is Need Render Serializer Block
     *
     * @return bool
     */
    private function isNeedRenderSerializer()
    {
        return !$this->requestDataProvider->getUniqId()
            && $this->requestDataProvider->getDisplayType() === Instance::GRID_DISPLAY_TYPE;
    }

    /**
     * Render Serializer Block
     *
     * @param BlockInterface $block
     * @return string
     */
    private function renderSerializer($block)
    {
        $serializer = $this->layout->createBlock(
            \Magento\Backend\Block\Widget\Grid\Serializer::class,
            '',
            [
                'data' => [
                    'grid_block' => $block,
                    'callback' => 'getSelectedNodes',
                    'input_element_name' => 'selected',
                    'reload_param_name' => 'selected',
                ]
            ]
        );

        return $serializer->toHtml();
    }
}