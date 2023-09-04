<?php
namespace Aheadworks\Bup\Model\ResourceModel\UserProfile;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aheadworks\Bup\Model\UserProfile;
use Aheadworks\Bup\Model\ResourceModel\UserProfile as UserProfileResource;

/**
 * Class Collection
 *
 * @package Aheadworks\Bup\Model\ResourceModel\UserProfile
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(UserProfile::class, UserProfileResource::class);
    }
}
