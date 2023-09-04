<?php
namespace Aheadworks\BlogSearch\Ui\DataProvider\ItemModifier;

/**
 * Interface ModifierInterface
 */
interface ModifierInterface
{
    /**
     * Modifies data provider item
     *
     * @param mixed $item
     * @return mixed
     */
    public function modify($item);
}
