<?php
namespace Aheadworks\Blog\Model\Author;

use Aheadworks\Blog\Model\ResourceModel\Validator\UrlKeyIsUnique;
use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\Blog\Model\Author;
use Aheadworks\Blog\Model\Validator\UrlKey as UrlKeyValidator;
use Magento\Framework\Validator\Regex;

/**
 * Class Validator
 * @package Aheadworks\Blog\Model\Author
 */
class Validator extends AbstractValidator
{
    /**
     * @var UrlKeyIsUnique
     */
    private $urlKeyIsUnique;

    /**
     * @var UrlKeyValidator
     */
    private $urlKeyValidator;

    /**
     * @param UrlKeyIsUnique $urlKeyIsUnique
     * @param UrlKeyValidator $urlKeyValidator
     */
    public function __construct(
        UrlKeyIsUnique $urlKeyIsUnique,
        UrlKeyValidator $urlKeyValidator
    ) {
        $this->urlKeyIsUnique = $urlKeyIsUnique;
        $this->urlKeyValidator = $urlKeyValidator;
    }

    /**
     * Validate required author data
     *
     * @param Author $author
     * @return bool
     * @throws \Zend_Validate_Exception
     * @throws \Exception
     */
    public function isValid($author)
    {
        $errors = [];
        $twitterIdValidator = new Regex('/^(\@)[A-Za-z0-9_]{1,15}$/i');
        $facebookIdValidator = new Regex('/[A-Za-z0-9_]{1,100}$/i');
        $linkedinIdValidator = new Regex('/[A-Za-z0-9_]{5,30}$/i');

        if (!empty($author->getTwitterId()) && !$twitterIdValidator->isValid($author->getTwitterId())) {
            $errors[] = __('Twitter ID is incorrect.');
        }
        if (!empty($author->getFacebookId()) && !$facebookIdValidator->isValid($author->getFacebookId())) {
            $errors[] = __('Facebook ID is incorrect.');
        }
        if (!empty($author->getLinkedinId()) && !$linkedinIdValidator->isValid($author->getLinkedinId())) {
            $errors[] = __('LinkedIn ID is incorrect.');
        }
        if (empty($author->getFirstname())) {
            $errors[] = __('First Name can\'t be empty.');
        }
        if (empty($author->getLastname())) {
            $errors[] = __('Last Name can\'t be empty.');
        }
        if (empty($author->getUrlKey())) {
            $errors[] = __('URL-key can\'t be empty.');
        }
        if (!$this->urlKeyIsUnique->validate($author)) {
            $errors[] = __('This URL-Key is already assigned to another post, author or category.');
        }
        if(!$this->urlKeyValidator->isValid($author->getUrlKey())) {
            $errors[] = __('URL-Key cannot contain capital letters or disallowed symbols.');
        }

        $this->_addMessages($errors);

        return empty($errors);
    }
}
