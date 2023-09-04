<?php
namespace Aheadworks\BlogSearch\Ui\DataProvider\ItemModifier;

/**
 * Class Composite
 */
class Composite implements ModifierInterface
{
    /**
     * @var ModifierInterface[]
     */
    private $modifiers;

    /**
     * Composite constructor.
     * @param array $modifiers
     */
    public function __construct(
        $modifiers = []
    ) {
        $this->modifiers = $modifiers;
    }

    /**
     * @inheritdoc
     */
    public function modify($item)
    {
        foreach ($this->modifiers as $modifier) {
            $item = $modifier->modify($item);
        }

        return $item;
    }
}
