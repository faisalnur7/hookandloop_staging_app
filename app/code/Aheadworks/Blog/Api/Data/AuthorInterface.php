<?php
namespace Aheadworks\Blog\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface AuthorInterface
 * @package Aheadworks\Blog\Api\Data
 */
interface AuthorInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const FIRSTNAME = 'firstname';
    const LASTNAME = 'lastname';
    const URL_KEY = 'url_key';
    const META_TITLE = 'meta_title';
    const META_KEYWORDS = 'meta_keywords';
    const META_DESCRIPTION = 'meta_description';
    const META_PREFIX = 'meta_prefix';
    const META_SUFFIX = 'meta_suffix';
    const JOB_POSITION = 'job_position';
    const IMAGE_FILE = 'image_file';
    const SHORT_BIO = 'short_bio';
    const TWITTER_ID = 'twitter_id';
    const FACEBOOK_ID = 'facebook_id';
    const LINKEDIN_ID = 'linkedin_id';
    /**#@-*/

    /**#@+
     * Attached columns keys
     */
    const POSTS_COUNT = 'posts_count';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstname();

    /**
     * Set first name
     *
     * @param string $name
     * @return $this
     */
    public function setFirstname($name);

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastname();

    /**
     * Set last name
     *
     * @param string $name
     * @return $this
     */
    public function setLastname($name);
    
    /**
     * Get URL-Key
     *
     * @return string
     */
    public function getUrlKey();

    /**
     * Set URL-Key
     *
     * @param string $urlKey
     * @return $this
     */
    public function setUrlKey($urlKey);

    /**
     * Get meta title
     *
     * @return string|null
     */
    public function getMetaTitle();

    /**
     * Set meta title
     *
     * @param string $metaTitle
     * @return $this
     */
    public function setMetaTitle($metaTitle);

    /**
     * Get meta keywords
     *
     * @return string|null
     */
    public function getMetaKeywords();

    /**
     * Set meta keywords
     *
     * @param string $metaKeywords
     * @return $this
     */
    public function setMetaKeywords($metaKeywords);

    /**
     * Get meta description
     *
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * Set meta description
     *
     * @param string $metaDescription
     * @return $this
     */
    public function setMetaDescription($metaDescription);

    /**
     * Get meta prefix
     *
     * @return string|null
     */
    public function getMetaPrefix();

    /**
     * Set meta prefix
     *
     * @param string $metaPrefix
     * @return $this
     */
    public function setMetaPrefix($metaPrefix);

    /**
     * Get meta suffix
     *
     * @return string|null
     */
    public function getMetaSuffix();

    /**
     * Set meta suffix
     *
     * @param string $metaSuffix
     * @return $this
     */
    public function setMetaSuffix($metaSuffix);

    /**
     * Get job position
     *
     * @return string
     */
    public function getJobPosition();

    /**
     * Set job position
     *
     * @param string $jobPosition
     * @return $this
     */
    public function setJobPosition($jobPosition);

    /**
     * Get image file
     *
     * @return string
     */
    public function getImageFile();

    /**
     * Set image file
     *
     * @param string $file
     * @return $this
     */
    public function setImageFile($file);

    /**
     * Get short bio
     *
     * @return string
     */
    public function getShortBio();

    /**
     * Set short bio
     *
     * @param string $shortBio
     * @return $this
     */
    public function setShortBio($shortBio);

    /**
     * Get twitter ID
     *
     * @return string
     */
    public function getTwitterId();

    /**
     * Set twitter ID
     *
     * @param string $twitterId
     * @return $this
     */
    public function setTwitterId($twitterId);

    /**
     * Get facebook ID
     *
     * @return string
     */
    public function getFacebookId();

    /**
     * Set facebook ID
     *
     * @param string $facebookId
     * @return $this
     */
    public function setFacebookId($facebookId);

    /**
     * Get linkedIn ID
     *
     * @return string
     */
    public function getLinkedinId();

    /**
     * Set linkedIn ID
     *
     * @param string $linkedinId
     * @return $this
     */
    public function setLinkedinId($linkedinId);

    /**
     * Get posts count
     *
     * @return int|null
     */
    public function getPostsCount();

    /**
     * Set posts count
     *
     * @param int $count
     * @return $this
     */
    public function setPostsCount($count);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Blog\Api\Data\AuthorExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Blog\Api\Data\AuthorExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Aheadworks\Blog\Api\Data\AuthorExtensionInterface $extensionAttributes);
}
