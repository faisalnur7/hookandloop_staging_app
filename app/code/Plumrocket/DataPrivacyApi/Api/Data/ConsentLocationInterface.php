<?php
/**
 * @package     Plumrocket_DataPrivacyApi
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\DataPrivacyApi\Api\Data;

/**
 * @since 2.0.0
 */
interface ConsentLocationInterface
{
    public const LOCATION_CHECKOUT = 'checkout';
    public const LOCATION_REGISTRATION = 'registration';
    public const LOCATION_NEWSLETTER = 'newsletter';
    public const LOCATION_CONTACT_US = 'contact_us';
    public const LOCATION_POPUP_NOTIFY = 'popup_notify';
    public const LOCATION_CUSTOM = 'prgdpr_custom';
    public const LOCATION_MY_ACCOUNT = 'my_account';

    public const LOCATION_KEY = 'location_key';
    public const TYPE = 'type';
    public const VISIBLE = 'visible';
    public const NAME = 'name';
    public const DESCRIPTION = 'description';

    /**
     * Get location id.
     *
     * @return string|int
     */
    public function getId();

    /**
     * Check is location is system that is not custom.
     *
     * @return bool
     */
    public function isSystem() : bool;

    /**
     * Check if location is visible for customers and admins.
     *
     * @return bool
     */
    public function isVisible() : bool;

    /**
     * Get location key - the main identifier.
     *
     * @return string
     */
    public function getLocationKey() : string;

    /**
     * Set location key.
     *
     * @param string $locationKey
     * @return \Plumrocket\DataPrivacyApi\Api\Data\ConsentLocationInterface
     */
    public function setLocationKey(string $locationKey) : self;

    /**
     * Set location type.
     *
     * @param int $type
     * @return ConsentLocationInterface
     */
    public function setType(int $type) : self;

    /**
     * Get location type.
     *
     * @return int
     */
    public function getType() : int;

    /**
     * Set visibility.
     *
     * @param bool $flag
     * @return ConsentLocationInterface
     */
    public function setVisibility(bool $flag) : self;

    /**
     * Get location visibility.
     *
     * @return bool
     */
    public function getVisibility() : bool;

    /**
     * Get name.
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Set name.
     *
     * @param string $name
     * @return ConsentLocationInterface
     */
    public function setName(string $name) : self;

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription() : string;

    /**
     * Set description.
     *
     * @param string $description
     * @return ConsentLocationInterface
     */
    public function setDescription(string $description) : self;
}
