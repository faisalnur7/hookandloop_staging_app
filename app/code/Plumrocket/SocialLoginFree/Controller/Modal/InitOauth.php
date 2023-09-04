<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Controller\Modal;

use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManager;
use Plumrocket\SocialLoginFree\Helper\Config;
use Plumrocket\SocialLoginFree\Helper\Config\Network as NetworkConfig;
use Plumrocket\SocialLoginFree\Model\Customer\LastViewedPageProvider;
use Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface;
use Plumrocket\SocialLoginFree\Model\Network\Debug\NetworkLoggerInterface;
use Plumrocket\SocialLoginFree\Model\Network\Exception\NetworkIsNotConfiguredException;
use Plumrocket\SocialLoginFree\Model\Network\Modal\Manager as ModalManager;
use Plumrocket\SocialLoginFree\Model\Network\Modal\UrlResolverFactory as ModalUrlResolverFactory;

/**
 * @since 3.8.0
 */
class InitOauth implements ActionInterface
{

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    private $resultFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\Modal\Manager
     */
    private $modalManager;

    /**
     * @var NetworkConfig
     */
    private $networkConfig;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface
     */
    private $apiCallParamsPersistor;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var ModalUrlResolverFactory
     */
    private $modalUrlResolverFactory;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Customer\LastViewedPageProvider
     */
    private $lastViewedPageProvider;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\Debug\NetworkLoggerInterface
     */
    private $networkLogger;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @param \Magento\Framework\Controller\ResultFactory                               $resultFactory
     * @param \Magento\Framework\App\RequestInterface                                   $request
     * @param \Plumrocket\SocialLoginFree\Helper\Config                                 $config
     * @param \Plumrocket\SocialLoginFree\Model\Network\Modal\Manager                   $modalManager
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Network                         $networkConfig
     * @param \Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface $apiCallParamsPersistor
     * @param \Magento\Store\Model\StoreManager                                         $storeManager
     * @param \Magento\Customer\Model\Session                                           $customerSession
     * @param \Plumrocket\SocialLoginFree\Model\Network\Modal\UrlResolverFactory        $modalUrlResolverFactory
     * @param \Plumrocket\SocialLoginFree\Model\Customer\LastViewedPageProvider         $lastViewedPageProvider
     * @param \Plumrocket\SocialLoginFree\Model\Network\Debug\NetworkLoggerInterface    $networkLogger
     * @param \Magento\Checkout\Model\Session                                           $checkoutSession
     */
    public function __construct(
        ResultFactory $resultFactory,
        RequestInterface $request,
        Config $config,
        ModalManager $modalManager,
        NetworkConfig $networkConfig,
        ApiCallParamsPersistorInterface $apiCallParamsPersistor,
        StoreManager $storeManager,
        Session $customerSession,
        ModalUrlResolverFactory $modalUrlResolverFactory,
        LastViewedPageProvider $lastViewedPageProvider,
        NetworkLoggerInterface $networkLogger,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->config = $config;
        $this->modalManager = $modalManager;
        $this->networkConfig = $networkConfig;
        $this->apiCallParamsPersistor = $apiCallParamsPersistor;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->modalUrlResolverFactory = $modalUrlResolverFactory;
        $this->lastViewedPageProvider = $lastViewedPageProvider;
        $this->networkLogger = $networkLogger;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Init OAuth modal.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(): ResultInterface
    {
        $this->customerSession->unsPsloginLog();

        $networkCode = $this->request->getParam('network') ?: $this->request->getParam('type', '');
        if (! $this->config->isModuleEnabled() || ! $this->networkConfig->isEnabled($networkCode)) {
            return $this->modalManager->close();
        }

        $forwardAction = $this->request->getParam('call');
        if (! $forwardAction && $this->customerSession->isLoggedIn()) {
            return $this->modalManager->close();
        }

        if ($forwardAction) {
            $this->apiCallParamsPersistor->add('type', $networkCode);
            $this->apiCallParamsPersistor->add('action', $forwardAction);
        }

        $customerAction = $this->request->getParam('customer_action');
        if ($customerAction) {
            $this->apiCallParamsPersistor->add('customer_action', $customerAction);
        }

        $this->apiCallParamsPersistor->add('pr_var_redirect_to', $this->request->getParam('pr_var_redirect_to', ''));

        /** @var \Magento\Framework\Controller\Result\Redirect $redirectResult */
        $redirectResult = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            $this->apiCallParamsPersistor->add('store', (int) $this->storeManager->getStore()->getId());
            $this->apiCallParamsPersistor->add('referer_url', $this->lastViewedPageProvider->getUrl());
            $this->apiCallParamsPersistor->addQuoteId((int) $this->checkoutSession->getQuoteId());

            $modalUrl = $this->modalUrlResolverFactory->create($networkCode)->getUrl();
            return $redirectResult->setUrl($modalUrl);
        } catch (NetworkIsNotConfiguredException|NoSuchEntityException $exception) {
            $this->networkLogger->addException($networkCode, $exception);
            return $this->modalManager->showCollectedErrors();
        }
    }
}
