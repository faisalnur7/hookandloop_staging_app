<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Layout;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout
 */
interface ProcessorInterface
{
    /**
     * Process js layout of block
     *
     * @param array $jsLayout
     * @param RendererInterface $renderer
     * @return array
     */
    public function process($jsLayout, $renderer);
}
