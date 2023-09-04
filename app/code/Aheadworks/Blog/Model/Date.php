<?php
declare(strict_types=1);

namespace Aheadworks\Blog\Model;

use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Date
 */
class Date
{
    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param DateTime $dateTime
     */
    public function __construct(
        DateTime $dateTime
    ) {
        $this->dateTime = $dateTime;
    }

    /**
     * Retrieve date by format
     *
     * @param  $format
     * @return false|string
     */
    public function getCurrentDate(string $format)
    {
        return $this->dateTime->gmtDate($format);
    }
}
