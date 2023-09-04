<?php
namespace Aheadworks\Helpdesk2\Model\Data\Validator\Rejection\Pattern;

use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Class Pattern
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Validator\Rejection\Pattern
 */
class Pattern extends AbstractValidator
{
    /**
     * Check if preg_match pattern have valid syntax
     *
     * @param RejectingPatternInterface $pattern
     * @return bool
     * @throws \Exception
     */
    public function isValid($pattern)
    {
        $this->_clearMessages();
        
        try {
            preg_match($pattern->getPattern(), 'some-string');
        } catch (\Exception $exception) {
            $this->_addMessages([__('Rejecting pattern has incorrect syntax')]);
        }

        return empty($this->getMessages());
    }
}
