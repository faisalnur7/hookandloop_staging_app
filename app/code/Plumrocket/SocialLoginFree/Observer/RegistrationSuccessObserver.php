<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Plumrocket\SocialLoginFree\Api\CustomerNetworksManagerInterface;
use Plumrocket\SocialLoginFree\Helper\Config;
use Plumrocket\SocialLoginFree\Helper\Config\Network;
use Plumrocket\SocialLoginFree\Model\Success\RedirectManager;
use Plumrocket\SocialLoginFree\Model\SharePopup;

/**
 * - Link social network user id to magento customer id
 * - load photo from social network
 * - Show share popup
 * - Modify after register redirect according to the configuration
 */
class RegistrationSuccessObserver implements ObserverInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Success\RedirectManager
     */
    private $successRedirectManager;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\SharePopup
     */
    private $sharePopup;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Network
     */
    private $networkConfig;

    /**
     * @var \Plumrocket\SocialLoginFree\Api\CustomerNetworksManagerInterface
     */
    private $customerNetworksManager;

    /**
     * @param \Magento\Customer\Model\Session                                  $customerSession
     * @param \Magento\Framework\App\RequestInterface                          $httpRequest
     * @param \Plumrocket\SocialLoginFree\Helper\Config                        $config
     * @param \Plumrocket\SocialLoginFree\Model\Success\RedirectManager        $successRedirectManager
     * @param \Plumrocket\SocialLoginFree\Model\SharePopup                     $sharePopup
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Network                $networkConfig
     * @param \Plumrocket\SocialLoginFree\Api\CustomerNetworksManagerInterface $customerNetworksManager
     */
    public function __construct(
        Session $customerSession,
        RequestInterface $httpRequest,
        Config $config,
        RedirectManager $successRedirectManager,
        SharePopup $sharePopup,
        Network $networkConfig,
        CustomerNetworksManagerInterface $customerNetworksManager
    ) {
        $this->session = $customerSession;
        $this->request = $httpRequest;
        $this->config = $config;
        $this->successRedirectManager = $successRedirectManager;
        $this->sharePopup = $sharePopup;
        $this->networkConfig = $networkConfig;
        $this->customerNetworksManager = $customerNetworksManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (! $this->config->isModuleEnabled()) {
            return;
        }

        $data = $this->session->getData('pslogin');
        $networkCode = (string) ($data['provider'] ?? '');
        $userId = (string) ($data['user_id'] ?? '');
        $photo = (string) ($data['photo'] ?? '');

        if (! empty($networkCode) && ! empty($userId) && ! empty($data['timeout']) && $data['timeout'] > time()) {
            if (! $this->networkConfig->isEnabled($networkCode)) {
                return;
            }

            if ($observer->getCustomer() && $observer->getCustomer()->getId()) {
                $customerId = (int) $observer->getCustomer()->getId();

                $this->customerNetworksManager->linkNetworkToCustomer(
                    $networkCode,
                    $userId,
                    $customerId,
                    $photo
                );
            }
            $this->session->unsPsloginFields();
        }

        $this->sharePopup->show();

        $this->request->setParam(
            RedirectInterface::PARAM_NAME_SUCCESS_URL,
            $this->successRedirectManager->getAfterRegisterUrl()
        );
    }
}
