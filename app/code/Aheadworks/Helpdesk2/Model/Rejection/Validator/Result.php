<?php
namespace Aheadworks\Helpdesk2\Model\Rejection\Validator;

/**
 * Class Result
 *
 * @package Aheadworks\Helpdesk2\Model\Rejection\Validator
 */
class Result
{
    /**
     * @var bool
     */
    private $isRejected = false;

    /**
     * @var int
     */
    private $patternId;

    /**
     * @return bool
     */
    public function isRejected()
    {
        return $this->isRejected;
    }

    /**
     * @param bool $isRejected
     * @return Result
     */
    public function setIsRejected(bool $isRejected)
    {
        $this->isRejected = $isRejected;
        return $this;
    }

    /**
     * @return int
     */
    public function getPatternId()
    {
        return $this->patternId;
    }

    /**
     * @param int $patternId
     * @return Result
     */
    public function setPatternId(int $patternId)
    {
        $this->patternId = $patternId;
        return $this;
    }
}
