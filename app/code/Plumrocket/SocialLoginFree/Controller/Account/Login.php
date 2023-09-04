<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Controller\Account;

use Exception;
use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Validation\ValidationException;
use Magento\Store\Model\StoreManager;
use Plumrocket\SocialLoginFree\Api\CreateCustomerFromNetworkAccountInterface;
use Plumrocket\SocialLoginFree\Api\CustomerNetworksManagerInterface;
use Plumrocket\SocialLoginFree\Exception\UserAccessDeniedException;
use Plumrocket\SocialLoginFree\Helper\Data;
use Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail;
use Plumrocket\SocialLoginFree\Model\Account\Photo;
use Plumrocket\SocialLoginFree\Model\Customer\GetCustomerIdByEmail;
use Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface;
use Plumrocket\SocialLoginFree\Model\Network\Debug\NetworkLoggerInterface;
use Plumrocket\SocialLoginFree\Model\Network\Modal\Manager;
use Plumrocket\SocialLoginFree\Model\SharePopup;
use Plumrocket\SocialLoginFree\Model\Success\RedirectManager;
use Plumrocket\SocialLoginFree\Api\GetNetworkConnectorInterface;
use Psr\Log\LoggerInterface;

class Login extends Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $_registry;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\Modal\Manager
     */
    private $modalWindowManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface
     */
    private $apiCallParamsPersistor;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail
     */
    private $fakeEmail;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Success\RedirectManager
     */
    private $successRedirectManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Plumrocket\SocialLoginFree\Api\GetNetworkConnectorInterface
     */
    private $getNetworkConnector;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\Debug\NetworkLoggerInterface
     */
    private $debugLogger;

    /**
     * @var \Plumrocket\SocialLoginFree\Api\CustomerNetworksManagerInterface
     */
    private $customerNetworksManager;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Photo
     */
    private $photo;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Customer\GetCustomerIdByEmail
     */
    private $getCustomerIdByEmail;

    /**
     * @var \Plumrocket\SocialLoginFree\Api\CreateCustomerFromNetworkAccountInterface
     */
    private $createCustomerFromNetworkAccount;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\SharePopup
     */
    private $sharePopup;

    /**
     * @var \Magento\Customer\Model\EmailNotificationInterface
     */
    private $customerEmailNotification;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @param \Magento\Framework\App\Action\Context                                     $context
     * @param \Magento\Customer\Model\Session                                           $customerSession
     * @param \Magento\Framework\Registry                                               $registry
     * @param \Plumrocket\SocialLoginFree\Model\Network\Modal\Manager                   $modalWindowManager
     * @param \Magento\Store\Model\StoreManager                                         $storeManager
     * @param \Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface $apiCallParamsPersistor
     * @param \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail                  $fakeEmail
     * @param \Plumrocket\SocialLoginFree\Model\Success\RedirectManager                 $successRedirectManager
     * @param \Psr\Log\LoggerInterface                                                  $logger
     * @param \Plumrocket\SocialLoginFree\Api\GetNetworkConnectorInterface              $getNetworkConnector
     * @param \Plumrocket\SocialLoginFree\Model\Network\Debug\NetworkLoggerInterface    $debugLogger
     * @param \Plumrocket\SocialLoginFree\Api\CustomerNetworksManagerInterface          $customerNetworksManager
     * @param \Plumrocket\SocialLoginFree\Model\Account\Photo                           $photo
     * @param \Plumrocket\SocialLoginFree\Model\Customer\GetCustomerIdByEmail           $getCustomerIdByEmail
     * @param \Plumrocket\SocialLoginFree\Api\CreateCustomerFromNetworkAccountInterface $createCustomerFromNetworkAccount
     * @param \Plumrocket\SocialLoginFree\Model\SharePopup                              $sharePopup
     * @param \Magento\Customer\Model\EmailNotificationInterface                        $customerEmailNotification
     * @param \Plumrocket\SocialLoginFree\Helper\Config                                 $config
     * @param \Magento\Newsletter\Model\SubscriberFactory                               $subscriberFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Registry $registry,
        Manager $modalWindowManager,
        StoreManager $storeManager,
        ApiCallParamsPersistorInterface $apiCallParamsPersistor,
        FakeEmail $fakeEmail,
        RedirectManager $successRedirectManager,
        LoggerInterface $logger,
        GetNetworkConnectorInterface $getNetworkConnector,
        NetworkLoggerInterface $debugLogger,
        CustomerNetworksManagerInterface $customerNetworksManager,
        Photo $photo,
        GetCustomerIdByEmail $getCustomerIdByEmail,
        CreateCustomerFromNetworkAccountInterface $createCustomerFromNetworkAccount,
        SharePopup $sharePopup,
        EmailNotificationInterface $customerEmailNotification,
        \Plumrocket\SocialLoginFree\Helper\Config $config,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
    ) {
        parent::__construct($context);
        $this->_registry = $registry;
        $this->modalWindowManager = $modalWindowManager;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->apiCallParamsPersistor = $apiCallParamsPersistor;
        $this->fakeEmail = $fakeEmail;
        $this->successRedirectManager = $successRedirectManager;
        $this->logger = $logger;
        $this->getNetworkConnector = $getNetworkConnector;
        $this->debugLogger = $debugLogger;
        $this->customerNetworksManager = $customerNetworksManager;
        $this->photo = $photo;
        $this->getCustomerIdByEmail = $getCustomerIdByEmail;
        $this->createCustomerFromNetworkAccount = $createCustomerFromNetworkAccount;
        $this->sharePopup = $sharePopup;
        $this->customerEmailNotification = $customerEmailNotification;
        $this->config = $config;
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $session = $this->customerSession;

        $isAjax = $this->getRequest()->isXmlHttpRequest();
        $type = $this->getRequest()->getParam('type', '');
        $forceRedirectTo = (string) $this->apiCallParamsPersistor->get('pr_var_redirect_to');
        // track action to know where we should redirect customer after login/registration
        $authAction = RedirectManager::AFTER_LOGIN;

        // API.
        $callTarget = false;
        $callParams = $this->apiCallParamsPersistor->get();
        if ($callParams && isset($callParams['type']) && $callParams['type'] === $type && $callParams['action']) {
            $target = explode('.', $callParams['action'], 3);
            if (count($target) === 3) {
                $callTarget = $target;
            } else {
                return $this->modalWindowManager->close($isAjax);
            }
        }

        if (! $callTarget && $this->customerSession->isLoggedIn()) {
            return $this->modalWindowManager->close($isAjax);
        }

        // Switch store.
        if ($storeId = $this->apiCallParamsPersistor->get('store')) {
            $this->storeManager->setCurrentStore($storeId);
        }

        try {
            $connector = $this->getNetworkConnector->execute($type);
        } catch (LocalizedException $localizedException) {
            $this->logger->critical($localizedException->getMessage());
            $this->messageManager->addErrorMessage(
                __('Sorry, but something went wrong while connecting social account.')
            );
            return $this->modalWindowManager->close($isAjax);
        }

        $response = $this->getRequest()->getParams();
        $this->debugLogger->add($type, $response);

        try {
            $networkAccount = $connector->getNetworkAccount($response);
        } catch (UserAccessDeniedException $e) {
            $redirectUrl = $this->successRedirectManager->getAfterLoginUrl($forceRedirectTo);
            return $this->modalWindowManager->redirect($isAjax, $redirectUrl, $authAction);
        } catch (LocalizedException $localizedException) {
            if ($this->_registry->registry('close_popup')) {
                return $this->modalWindowManager->close($isAjax);
            }

            $this->debugLogger->recordLogs();
            $result = $this->modalWindowManager->showCollectedErrors();
            $this->debugLogger->clear();
            return $result;
        }

        // API.
        if ($callTarget) {
            [$module, $controller, $action] = $callTarget;
            $this->_forward($action, $controller, $module, ['pslogin' => $networkAccount->toArray()]);
            return;
        }

        $customerId = $this->customerNetworksManager->getCustomerIdByNetwork(
            $networkAccount->getNetworkCode(),
            $networkAccount->getId()
        );

        if ($customerId) {
            # Social Network Already Linked
            $this->fakeEmail->changeToReal($customerId, $networkAccount->getEmail());
            if ($networkAccount->getPhotoUrl()) {
                try {
                    $this->photo->saveExternal($customerId, $networkAccount->getPhotoUrl());
                } catch (ValidationException $e) {
                    $this->logger->debug($e->getMessage());
                }
            }
            $redirectUrl = $this->successRedirectManager->getAfterLoginUrl($forceRedirectTo);
        } elseif ($customerId = $this->getCustomerIdByEmail->execute($networkAccount->getEmail())) {
            # Customer with received email was placed in db.

            try {
                $this->customerNetworksManager->linkNetworkToCustomer(
                    $networkAccount->getNetworkCode(),
                    $networkAccount->getId(),
                    $customerId,
                    $networkAccount->getPhotoUrl(),
                    $networkAccount->getAdditionalData()
                );
                $this->messageManager->addComplexNoticeMessage(
                    'resetPasswordMessage',
                    [
                        'email' => $networkAccount->getEmail(),
                        'url' => $this->_url->getUrl('customer/account/forgotpassword', [])
                    ]
                );
            } catch (LocalizedException $exception) {
                $this->logger->critical($exception);
                $this->messageManager->addErrorMessage(
                    __('Sorry, but something went wrong while connecting social account.')
                );
            }

            $redirectUrl = $this->successRedirectManager->getAfterLoginUrl();
        } else {
            # Register customer.

            try {
                $customer = $this->createCustomerFromNetworkAccount->execute($networkAccount);
                $customerId = (int) $customer->getId();

                if ($this->fakeEmail->detect($customer->getEmail())) {
                    $this->messageManager->addSuccessMessage(__('Customer registration successful.'));
                } else {
                    $this->messageManager->addSuccessMessage(
                        __(
                            'Customer registration successful. Your password was sent to %1',
                            $networkAccount->getEmail()
                        )
                    );
                }

                // Dispatch event.
                $this->_eventManager->dispatch(
                    'customer_register_success',
                    ['account_controller' => $this, 'customer' => $customer]
                );

                $this->customerNetworksManager->linkNetworkToCustomer(
                    $networkAccount->getNetworkCode(),
                    $networkAccount->getId(),
                    $customerId,
                    $networkAccount->getPhotoUrl(),
                    $networkAccount->getAdditionalData()
                );

                // Post mail.
                if (! $this->fakeEmail->detect((string) $customer->getEmail())) {
                    $this->customerEmailNotification->newAccount(
                        $customer,
                        EmailNotificationInterface::NEW_ACCOUNT_EMAIL_REGISTERED,
                        '',
                        $this->storeManager->getStore()->getId()
                    );
                }

                if ($this->config->isEnabledSubscription()
                    && ! $this->fakeEmail->detect($customer->getEmail())
                ) {
                    $this->subscriberFactory->create()->subscribeCustomerById($customerId);
                }

                // Show share-popup.
                $this->sharePopup->show();

                $authAction = RedirectManager::AFTER_REGISTER;
                $redirectUrl = $this->successRedirectManager->getAfterRegisterUrl($forceRedirectTo);
            } catch (Exception $e) {
                # Error.
                $session->setCustomerFormData($networkAccount->getCustomerData());
                $session->setPsloginFields($networkAccount->toArray());
                $redirectUrl = $this->_url->getUrl('customer/account/create', ['_secure' => true]);

                $this->messageManager->addErrorMessage($e->getMessage());

                // Remember current provider data.
                $session->setData('pslogin', [
                    'provider'  => $networkAccount->getNetworkCode(),
                    'user_id'   => $networkAccount->getId(),
                    'photo'     => $networkAccount->getPhotoUrl(),
                    'timeout'   => time() + Data::TIME_TO_EDIT,
                ]);
            }
        }

        if ($customerId) {
            // Logged in.
            if ($session->loginById($customerId)) {
                $session->regenerateId();
            }
            $this->successRedirectManager->unsetRefererUrl();
        }

        $this->debugLogger->clear();
        return $this->modalWindowManager->redirect($isAjax, $redirectUrl, $authAction);
    }
}
