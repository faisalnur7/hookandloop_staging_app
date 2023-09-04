<?php
namespace Aheadworks\BlogSearch\Model\Validator;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\BlogSearch\Model\Config as BlogSearchConfig;

/**
 * Class SearchQueryLength
 */
class SearchQueryLength extends AbstractValidator
{
    /**
     * @var BlogSearchConfig
     */
    private $blogSearchConfig;

    /**
     * SearchQueryLength constructor.
     * @param BlogSearchConfig $blogSearchConfig
     */
    public function __construct(
        BlogSearchConfig $blogSearchConfig
    ) {
        $this->blogSearchConfig = $blogSearchConfig;
    }

    /**
     * @param string $queryString
     * @return bool
     */
    public function isValid($queryString)
    {
        $this->_clearMessages();

        $searchQueryMinLength = $this->blogSearchConfig->getSearchQueryMinLength();
        $searchQueryMaxLength = $this->blogSearchConfig->getSearchQueryMaxLength();
        $searchQueryLength = strlen(trim($queryString));

        if ($searchQueryLength === 0) {
            $message = __('Search query cannot be empty');
            $this->_addMessages([$message]);
        }

        if ($searchQueryMinLength && $searchQueryLength < $searchQueryMinLength) {
            $message = __('Minimum Search query length is %1', $searchQueryMinLength);
            $this->_addMessages([$message]);
        }

        if ($searchQueryMaxLength && $searchQueryLength > $searchQueryMaxLength) {
            $message = __('Maximum Search query length is %1', $searchQueryMaxLength);
            $this->_addMessages([$message]);
        }

        return empty($this->getMessages());
    }
}
