<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Controller\Account;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface;
use Plumrocket\SocialLoginFree\Model\Success\RedirectManager;

class LoginPost extends Action
{

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var RedirectManager
     */
    private $redirectManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface
     */
    private $apiCallParamsPersistor;

    /**
     * @param \Magento\Framework\App\Action\Context                                     $context
     * @param \Magento\Customer\Model\Session                                           $customerSession
     * @param \Plumrocket\SocialLoginFree\Model\Success\RedirectManager                 $redirectManager
     * @param \Magento\Checkout\Model\Session                                           $checkoutSession
     * @param \Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface $apiCallParamsPersistor
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        RedirectManager $redirectManager,
        CheckoutSession $checkoutSession,
        ApiCallParamsPersistorInterface $apiCallParamsPersistor
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->redirectManager = $redirectManager;
        $this->checkoutSession = $checkoutSession;
        $this->apiCallParamsPersistor = $apiCallParamsPersistor;
    }

    /**
     * Redirect customer to necessary page.
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (! $this->checkoutSession->getQuoteId() && $this->apiCallParamsPersistor->getQuoteId()) {
            $this->checkoutSession->setQuoteId($this->apiCallParamsPersistor->getQuoteId());
            $this->checkoutSession->loadCustomerQuote();
        }
        $customRedirect = (string) $this->apiCallParamsPersistor->get('pr_var_redirect_to');
        $this->apiCallParamsPersistor->clear();

        if ($this->customerSession->getBeforeRequestParams()) {
            $result = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $result->setParams($this->customerSession->getBeforeRequestParams())
                   ->setModule($this->customerSession->getBeforeModuleName())
                   ->setController($this->customerSession->getBeforeControllerName())
                   ->forward($this->customerSession->getBeforeAction());
            return $result;
        }

        $authAction = $this->getRequest()->getParam('auth_action', '');
        if (in_array($authAction, ['login', 'register'], true)) {
            $redirectUrl = $this->redirectManager->getRedirectUrl($authAction, $customRedirect);
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($redirectUrl);
        }

        $redirectUrl = $this->getRequest()->getParam(Url::REFERER_QUERY_PARAM_NAME);
        $redirectUrl = base64_decode($redirectUrl);

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($redirectUrl);
    }

    /**
     * Perform custom request validation.
     *
     * Return null if default validation is needed.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function validateForCsrf(RequestInterface $request)
    {
        return true;
    }

    /**
     * Create exception in case CSRF validation failed.
     *
     * Return null if default exception will suffice.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return null
     */
    public function createCsrfValidationException(RequestInterface $request)
    {
        return null;
    }
}
