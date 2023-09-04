<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Layout;

/**
 * Class ProcessorComposite
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout
 */
class ProcessorComposite implements ProcessorInterface
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param ProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * @inheritdoc
     */
    public function process($jsLayout, $renderer)
    {
        foreach ($this->processors as $processor) {
            $jsLayout = $processor->process($jsLayout, $renderer);
        }

        return $jsLayout;
    }
}
