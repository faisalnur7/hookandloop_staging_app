<?php
namespace Aheadworks\Blog\Model\Import\Data\Processor\Post;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;
use Aheadworks\Blog\Model\StoreProvider;

/**
 * Class StoreIds
 */
class StoreIds implements ProcessorInterface
{
    const STORE_VIEW = 'store_view';

    /**
     * @var StoreProvider
     */
    private $storeProvider;

    /**
     * StoreIds constructor.
     * @param StoreProvider $storeProvider
     */
    public function __construct(
        StoreProvider $storeProvider
    ) {
        $this->storeProvider = $storeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if ($this->storeProvider->hasSingleStore()) {
            $data[PostInterface::STORE_IDS] = [$this->storeProvider->getCurrentStoreId()];
        } elseif(isset($data[self::STORE_VIEW]) && !empty($data[self::STORE_VIEW])) {
            $storeCodes = explode(',', (string)$data[self::STORE_VIEW]);
            $storeIds = $this->replaceStoreCodesOnIds($storeCodes);
            $data[PostInterface::STORE_IDS] = $storeIds;
        }

        return $data;
    }

    /**
     * Replace Store Codes on Store Ids
     *
     * @param array $storeCodes
     * @return array
     */
    public function replaceStoreCodesOnIds($storeCodes)
    {
        foreach ($storeCodes as $code) {
            $code = trim($code);
            if (is_numeric($code)) {
                $storeIds[] = $code;
                continue;
            }

            if ($storeId = $this->storeProvider->getStoreIdByCode($code)) {
                $storeIds[] = $storeId;
            }
        }

        return $storeIds;
    }
}
