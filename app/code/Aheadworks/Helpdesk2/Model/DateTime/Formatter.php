<?php
namespace Aheadworks\Helpdesk2\Model\DateTime;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Formatter
{
    /**
     * @param TimezoneInterface $timezoneInterface
     * @param string $dateFormat
     */
    public function __construct(
        private TimezoneInterface $timezoneInterface,
        private string $dateFormat = 'M j, Y h:i:s A'
    ) {
    }

    /**
     * Format date(UTC) to default scope specified
     *
     * @param string $date
     * @return string
     */
    public function formatDate($date)
    {
        //todo add timezone
        $date = new \DateTime($date ?? 'now');
        return $date->format($this->dateFormat);
    }

    /**
     * Get current date and timezone
     *
     * @return string
     * @throws \Exception
     */
    public function getCurrentDateAndTimezoneAsString(): string
    {
        $timezone = $this->timezoneInterface->getConfigTimezone(
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
        $date = new \DateTime(
            'now',
            new \DateTimeZone($timezone)
        );

        return $date->format($this->dateFormat) .' ('. $timezone .')';
    }

    /**
     * Convert date to format
     *
     * @param string $date
     * @param string $format
     * @return string
     * @throws \Exception
     */
    public function convertDateToFormat(string $date, string $format = 'M d, Y'): string
    {
        $date = $this->timezoneInterface->date(new \DateTime($date));
        return $date->format($format);
    }
}
