<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Admin Actions Log for Magento 2
 */

namespace Amasty\AdminActionsLog\Restoring;

use Amasty\AdminActionsLog\Api\Data\LogEntryInterface;
use Amasty\AdminActionsLog\Model\OptionSource\LogEntryTypes;

class RestoreValidator
{
    /**
     * @var array
     */
    private $notRestorableCategories;

    public function __construct(array $notRestorableCategories)
    {
        $this->notRestorableCategories = $notRestorableCategories;
    }

    public function isValid(LogEntryInterface $logEntry): bool
    {
        return $logEntry->getType() == LogEntryTypes::TYPE_EDIT
            && !in_array($logEntry->getCategory(), $this->notRestorableCategories);
    }
}
