<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\ResourceModel\AccountLink;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Plumrocket\SocialLoginFree\Model\Network\Data\AccountLink;
use Plumrocket\SocialLoginFree\Model\ResourceModel\AccountLink as AccountLinkResource;

/**
 * @since 4.0.0
 * @method \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountLinkInterface[] getItems()
 */
class Collection extends AbstractCollection
{

    /**
     * Init collection.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            AccountLink::class,
            AccountLinkResource::class
        );
    }

    /**
     * Get code => title array.
     *
     * @return array
     */
    public function toOptionIdArray()
    {
        $options = [];
        foreach ($this->getItems() as $item) {
            $options[] = [
                'value' => $item->getNetworkCode(),
                'label' => $item->getNetworkTitle(),
            ];
        }

        return $options;
    }
}
