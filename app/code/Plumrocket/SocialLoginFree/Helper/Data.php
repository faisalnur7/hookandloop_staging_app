<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Helper;

use Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface;
use Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail;
use Plumrocket\SocialLoginFree\Model\Account\Photo;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const TIME_TO_EDIT = 300;

    /**
     * @var string
     */
    protected $_configSectionId = Config::SECTION_ID;

    /**
     * @var null | array
     */
    private $_buttons = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Photo
     */
    private $photo;

    /**
     * @var \Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface
     */
    private $networkButtonProvider;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\JsLayout
     */
    private $jsLayoutHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context                          $context
     * @param \Magento\Customer\Model\Session                                $customerSession
     * @param \Plumrocket\SocialLoginFree\Helper\Config                      $config
     * @param \Plumrocket\SocialLoginFree\Model\Account\Photo                $photo
     * @param \Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface $networkButtonProvider
     * @param \Plumrocket\SocialLoginFree\Helper\JsLayout                    $jsLayoutHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        Config $config,
        Photo $photo,
        NetworkButtonProviderInterface $networkButtonProvider,
        JsLayout $jsLayoutHelper
    ) {
        parent::__construct($context);
        $this->customerSession  = $customerSession;
        $this->_configSectionId = Config::SECTION_ID;
        $this->config = $config;
        $this->photo = $photo;
        $this->networkButtonProvider = $networkButtonProvider;
        $this->jsLayoutHelper = $jsLayoutHelper;
    }

    /**
     * @return bool
     */
    public function hasButtons()
    {
        if (! $this->config->isModuleEnabled()) {
            return false;
        }

        if ($this->customerSession->isLoggedIn()) {
            return false;
        }

        return (bool) $this->getButtons();
    }

    /**
     * @deprecated since 4.0.0
     * @see \Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface::getDefaultList
     * @return array
     */
    public function getButtons()
    {
        if (null === $this->_buttons) {
            $this->_buttons = $this->networkButtonProvider->getDefaultList();
        }
        return $this->_buttons;
    }

    public function moduleInvitationsEnabled()
    {
        return false;
    }

    /**
     * @deprecated since 4.0.0
     * @see \Plumrocket\SocialLoginFree\Helper\JsLayout::getCheckoutAuthenticationComponent()
     * @return string
     */
    public function getCheckoutJsViewAuthentication(): string
    {
        return $this->jsLayoutHelper->getCheckoutAuthenticationComponent();
    }

    /**
     * @deprecated since 4.0.0
     * @see \Plumrocket\SocialLoginFree\Helper\JsLayout::getCheckoutEmailComponent()
     * @return string
     */
    public function getCheckoutJsViewFormElementEmail(): string
    {
        return $this->jsLayoutHelper->getCheckoutEmailComponent();
    }

    /**
     * @deprecated since 4.0.0
     * @see \Plumrocket\SocialLoginFree\Helper\JsLayout::getCustomerAuthenticationPopupComponent()
     * @return string
     */
    public function getCustomerJsViewAuthenticationPopup(): string
    {
        return $this->jsLayoutHelper->getCustomerAuthenticationPopupComponent();
    }

    /**
     * @deprecated
     * @see \Plumrocket\SocialLoginFree\Model\Account\Photo::getPhotoUrl
     * @param bool $checkIsEnabled
     * @param null $customerId
     * @return bool|string
     */
    public function getPhotoPath($checkIsEnabled = true, $customerId = null)
    {
        if ($checkIsEnabled && ! $this->config->isPhotoEnabled()) {
            return false;
        }

        if ($customerId === null) {
            if (! $this->customerSession->isLoggedIn()) {
                return false;
            }

            if (! $customerId = $this->customerSession->getCustomerId()) {
                return false;
            }
        } elseif (!is_numeric($customerId) || $customerId <= 0) {
            return false;
        }

        return $this->photo->getPhotoUrl($customerId);
    }

    /**
     * @param string|null $email
     * @return bool
     */
    public function isFakeMail(?string $email = null): bool
    {
        if (null === $email && $this->customerSession->isLoggedIn()) {
            $email = $this->customerSession->getCustomer()->getEmail();
        }

        return strpos($email, FakeEmail::FAKE_EMAIL_PREFIX) === 0;
    }
}
