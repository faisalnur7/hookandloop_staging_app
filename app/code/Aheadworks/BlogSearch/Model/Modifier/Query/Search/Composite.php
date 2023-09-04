<?php
namespace Aheadworks\BlogSearch\Model\Modifier\Query\Search;

use Magento\Framework\Search\RequestInterface;

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
    public function modify(array $searchQuery, RequestInterface $request)
    {
        foreach ($this->modifiers as $modifier) {
            $searchQuery = $modifier->modify($searchQuery, $request);
        }

        return $searchQuery;
    }
}
