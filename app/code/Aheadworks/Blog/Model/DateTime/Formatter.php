<?php
namespace Aheadworks\Blog\Model\DateTime;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;

/**
 * Class Formatter
 *
 * @package Aheadworks\Blog\Model\DateTime
 */
class Formatter
{
    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @param TimezoneInterface $localeDate
     */
    public function __construct(
        TimezoneInterface $localeDate
    ) {
        $this->localeDate = $localeDate;
    }

    /**
     * Retrieve formatted date and time, localized according to the specific store
     *
     * @param string|null $date
     * @param int|null $storeId
     * @param string $format
     * @return string
     */
    public function getLocalizedDateTime($date = null, $storeId = null, $format = StdlibDateTime::DATETIME_PHP_FORMAT)
    {
        $scopeDate = $this->localeDate->scopeDate($storeId, $date, true);
        return $scopeDate->format($format);
    }

    /**
     * Retrieve formatted date
     *
     * @param string $date
     * @param int $hour
     * @param int $minute
     * @param int $second
     */
    public function getDate($date, $hour = 0, $minute = 0, $second = 0)
    {
        $dateTime = new \DateTime($date);
        $dateTime->setTime($hour, $minute, $second);

        return $dateTime->format(StdlibDateTime::DATETIME_PHP_FORMAT);
    }
}
