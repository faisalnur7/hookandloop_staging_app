<?php
namespace Aheadworks\Blog\Model\Validator;

use Magento\Framework\Validator\NotEmpty;

/**
 * Validate Url-keys
 */
class UrlKey extends NotEmpty
{
    const IS_EMPTY = 'isEmpty';
    const IS_NUMBER = 'isNumber';
    const CONTAINS_DISALLOWED_SYMBOLS = 'containsDisallowedSymbols';
    const INVALID  = 'urlKeyInvalid';

    /**
     * @var array
     */
    protected $_messageTemplates = [
        self::IS_EMPTY => 'Value is required and can\'t be empty',
        self::IS_NUMBER  => 'Value consists of numbers',
        self::CONTAINS_DISALLOWED_SYMBOLS  => 'Value contains disallowed symbols',
        self::INVALID  => 'Invalid type given. String expected',
    ];

    public function __construct($options = null) {
        !isset($this->abstractOptions) ?:
            $this->abstractOptions['messageTemplates'] = $this->_messageTemplates;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->addErrorMessage(self::INVALID);
            return false;
        }
        if ($value == '') {
            $this->addErrorMessage(self::IS_EMPTY);
            return false;
        }
        if (preg_match('/^[0-9]+$/', (string)$value)) {
            $this->addErrorMessage(self::IS_NUMBER);
            return false;
        }
        if (!preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', (string)$value)) {
            $this->addErrorMessage(self::CONTAINS_DISALLOWED_SYMBOLS);
            return false;
        }

        return true;
    }

    /**
     * Add error message
     *
     * @param string $message
     * @return void
     */
    private function addErrorMessage(string $message): void
    {
        isset($this->_errors) ? $this->_error($message) : $this->error($message);
    }
}
