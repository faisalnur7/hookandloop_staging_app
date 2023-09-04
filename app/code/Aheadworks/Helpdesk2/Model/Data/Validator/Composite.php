<?php
namespace Aheadworks\Helpdesk2\Model\Data\Validator;

use Magento\Framework\Validator\AbstractValidator;

/**
 * Class Composite
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Validator
 */
class Composite extends AbstractValidator
{
    /**
     * @var AbstractValidator[]
     */
    private $validators;

    /**
     * @param AbstractValidator[] $validators
     */
    public function __construct(array $validators = [])
    {
        $this->validators = $validators;
    }

    /**
     * @inheritdoc
     */
    public function isValid($abstractModel)
    {
        $this->_clearMessages();

        foreach ($this->validators as $validator) {
            if (!$validator->isValid($abstractModel)) {
                $this->_addMessages($validator->getMessages());
            }
        }

        return empty($this->getMessages());
    }
}
