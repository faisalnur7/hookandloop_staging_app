<?php
namespace Aheadworks\Blog\Model\Indexer\Cache\Processor;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\EntityManager\EventManager;

/**
 * Class Cleaner
 */
class Cleaner
{
    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var CacheInterface
     */
    private $appCache;

    /**
     * @param EventManager $eventManager
     * @param CacheInterface $appCache
     */
    public function __construct(
        EventManager $eventManager,
        CacheInterface $appCache
    ) {
        $this->eventManager = $eventManager;
        $this->appCache = $appCache;
    }

    /**
     * Clean cache with the instance of IdentityInterface
     *
     * @param IdentityInterface $identity
     * @return $this
     */
    public function execute($identity)
    {
        $this->eventManager->dispatch(
            'clean_cache_by_tags',
            [
                'object' => $identity
            ]
        );
        $this->appCache->clean(
            $identity->getIdentities()
        );
        return $this;
    }
}