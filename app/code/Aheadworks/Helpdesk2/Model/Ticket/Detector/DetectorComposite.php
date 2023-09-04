<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Detector;

/**
 * Class DetectorComposite
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Detector
 */
class DetectorComposite implements DetectorInterface
{
    /**
     * @var DetectorInterface[]
     */
    private $detectors;

    /**
     * @param DetectorInterface[] $detectors
     */
    public function __construct(array $detectors = [])
    {
        $this->detectors = $detectors;
    }

    /**
     * @inheritdoc
     */
    public function detect($dataToDetect)
    {
        foreach ($this->detectors as $detector) {
            $detector->detect($dataToDetect);
        }
    }
}
