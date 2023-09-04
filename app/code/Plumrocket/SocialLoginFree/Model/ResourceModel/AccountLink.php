<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @since 4.0.0
 */
class AccountLink extends AbstractDb
{
    /** Name of Primary Column */
    public const ID_FIELD_NAME = 'id';
    public const MAIN_TABLE = 'plumrocket_sociallogin_account';

    /**
     * @var array
     */
    protected $_serializableFields = ['additional' => [[], []]];

    /**
     * Init table and id field.
     */
    public function _construct()
    {
        $this->_init(self::MAIN_TABLE, self::ID_FIELD_NAME);
    }

    /**
     * Get customer id by network account id.
     *
     * @param string   $networkCode
     * @param string   $userId
     * @param int|null $websiteId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerIdByNetworkId(string $networkCode, string $userId, int $websiteId = null): int
    {
        $connection = $this->getConnection();

        if (null === $websiteId) {
            $bind = ['type' => $networkCode, 'userId' => $userId];

            $select = $connection->select()->from(
                $this->getTable($this->getMainTable()),
                ['customer_id']
            )->where(
                'type = :type'
            )->where(
                'user_id = :userId'
            )->limit(
                1
            );
        } else {
            $bind = ['type' => $networkCode, 'userId' => $userId, 'website_id' => $websiteId];

            $select = $connection->select()->from(
                ['main_table' => $this->getTable($this->getMainTable())],
                ['customer_id']
            )->where(
                'type = :type'
            )->where(
                'user_id = :userId'
            )->join(
                ['customer' => $this->getTable('customer_entity')],
                'main_table.customer_id = customer.entity_id',
                'email'
            )->where(
                'customer.website_id = :website_id'
            )->limit(
                1
            );
        }

        return (int) $connection->fetchOne($select, $bind);
    }
}
