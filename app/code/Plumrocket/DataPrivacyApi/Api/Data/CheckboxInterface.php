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
interface CheckboxInterface
{
    public const STORE_ID = 'store_id';
    public const STATUS = 'status';
    public const LOCATION_KEY = 'location_key';
    public const LABEL = 'label';
    public const CMS_PAGE_ID = 'cms_page_id';
    public const REQUIRE = 'require';
    public const GEO_TARGETING = 'geo_targeting';
    public const GEO_TARGETING_USA_STATES = 'geo_targeting_usa_states';
    public const INTERNAL_NOTE = 'internal_note';

    /**
     * Get id.
     *
     * @return string|int
     */
    public function getId();

    /**
     * Check if checkbox si required.
     *
     * @return bool
     */
    public function isRequiredForValidate(): bool;

    /**
     * Get status (enabled/disabled)
     *
     * @return bool
     */
    public function getStatus(): bool;

    /**
     * Set status.
     *
     * @param bool $status
     * @return \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface
     */
    public function setStatus(bool $status): CheckboxInterface;

    /**
     * Get location keys on which checkbox should be showed.
     *
     * @return array
     */
    public function getLocationKeys(): array;

    /**
     * Set location keys.
     *
     * @param array $locationKeys
     * @return \Plumrocket\DataPrivacyApi\Api\Data\CheckboxInterface
     */
    public function setLocationKeys(array $locationKeys): CheckboxInterface;

    /**
     * Is should be showed on location.
     *
     * @param string $locationKey
     * @return bool
     */
    public function isUsedInLocation(string $locationKey): bool;

    /**
     * Get html label.
     *
     * @param bool $formatLabel
     * @return string
     */
    public function getLabel(bool $formatLabel = true): string;

    /**
     * Set label.
     *
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label): CheckboxInterface;

    /**
     * Get is required.
     *
     * @return bool
     */
    public function getRequire(): bool;

    /**
     * Set is required checkbox.
     *
     * @param bool $isRequired
     * @return $this
     */
    public function setRequire(bool $isRequired): CheckboxInterface;

    /**
     * Get geo targeting settings.
     *
     * @return array
     */
    public function getGeoTargeting(): array;

    /**
     * Get geo targeting settings.
     *
     * @return array
     */
    public function getGeoTargetingUsaStates(): array;

    /**
     * Set geo targeting settings.
     *
     * @param array $geoTargeting
     * @return $this
     */
    public function setGeoTargeting(array $geoTargeting): CheckboxInterface;

    /**
     * Get internal link.
     *
     * @return string
     */
    public function getInternalNote(): string;

    /**
     * Set internal link.
     *
     * @param string $internalNote
     * @return $this
     */
    public function setInternalNote(string $internalNote): CheckboxInterface;

    /**
     * Retrieve Store Id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set checkbox store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId): CheckboxInterface;

    /**
     * Get policy if bounded.
     *
     * @return \Plumrocket\DataPrivacyApi\Api\Data\PolicyInterface|null
     */
    public function getPolicy(): ?PolicyInterface;
}
