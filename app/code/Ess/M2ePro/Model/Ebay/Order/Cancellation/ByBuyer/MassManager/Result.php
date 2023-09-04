<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\Ebay\Order\Cancellation\ByBuyer\MassManager;

class Result
{
    /** @var int */
    private $success;
    /** @var int */
    private $errors;
    /** @var int */
    private $notAllowed;

    public function __construct(
        int $success,
        int $errors,
        int $notAllowed
    ) {
        $this->success = $success;
        $this->errors = $errors;
        $this->notAllowed = $notAllowed;
    }

    public function getSuccess(): int
    {
        return $this->success;
    }

    public function getErrors(): int
    {
        return $this->errors;
    }

    public function getNotAllowed(): int
    {
        return $this->notAllowed;
    }
}
