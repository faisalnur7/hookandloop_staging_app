<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Detector;

/**
 * Interface DetectorInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Detector
 */
interface DetectorInterface
{
    /**
     * Detect changes depending on type
     *
     * @param array $dataToDetect
     */
    public function detect($dataToDetect);
}
