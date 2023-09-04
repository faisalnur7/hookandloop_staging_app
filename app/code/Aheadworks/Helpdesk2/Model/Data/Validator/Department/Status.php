<?php
namespace Aheadworks\Helpdesk2\Model\Data\Validator\Department;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\Department\ConfigChecker;

/**
 * Class Status
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Validator\Department
 */
class Status extends AbstractValidator
{
    /**
     * @var ConfigChecker
     */
    private $configChecker;

    /**
     * @param ConfigChecker $configChecker
     */
    public function __construct(
        ConfigChecker $configChecker
    ) {
        $this->configChecker = $configChecker;
    }

    /**
     * Check can department be disabled
     *
     * @param DepartmentInterface $department
     * @return bool
     * @throws \Exception
     */
    public function isValid($department)
    {
        $this->_clearMessages();

        if (!$department->getIsActive()) {
            if ($this->configChecker->isSetAsPrimary($department->getId())) {
                $this->_addMessages(
                    [
                        __(
                            'Department "%1" set as primary in the extension settings and cannot be disabled',
                            $department->getName()
                        )
                    ]
                );
            }
        }

        return empty($this->getMessages());
    }
}
