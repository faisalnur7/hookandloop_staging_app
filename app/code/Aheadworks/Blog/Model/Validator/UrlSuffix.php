<?php
namespace Aheadworks\Blog\Model\Validator;

use Magento\Framework\Validator\AbstractValidator;

/**
 * Class UrlSuffix
 * @package Aheadworks\Blog\Model\Validator
 */
class UrlSuffix extends AbstractValidator
{
    /**
     * @param string $urlSuffixString
     * @return bool
     */
    public function isValid($urlSuffixString)
    {
        $this->_clearMessages();

        if (!preg_match('/^[a-z0-9\s_.\/-]*$/', (string)$urlSuffixString)) {
            $message = __('String contains disallowed characters');
            $this->_addMessages([$message]);
        }

        return empty($this->getMessages());
    }
}
