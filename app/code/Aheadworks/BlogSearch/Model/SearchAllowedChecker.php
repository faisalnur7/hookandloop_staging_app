<?php
namespace Aheadworks\BlogSearch\Model;

use Magento\Framework\Search\EngineResolverInterface;

/**
 * Class SearchAllowedChecker
 */
class SearchAllowedChecker
{
    const ALLOWED_ENGINES = ['elasticsearch6', 'elasticsearch7'];

    /**
     * @var EngineResolverInterface
     */
    private $engineResolver;

    /**
     * SearchAllowedChecker constructor.
     * @param EngineResolverInterface $engineResolver
     */
    public function __construct(
        EngineResolverInterface $engineResolver
    ) {
        $this->engineResolver = $engineResolver;
    }

    /**
     * Check if search allowed
     *
     * @returns bool
     */
    public function execute()
    {
        return $this->isCurrentSearchEngineAllowed();
    }

    /**
     * Checks if current search engine allowed
     *
     * @return bool
     */
    private function isCurrentSearchEngineAllowed()
    {
        $currentSearchEngine = $this->engineResolver->getCurrentSearchEngine();

        return in_array($currentSearchEngine, self::ALLOWED_ENGINES);
    }
}
