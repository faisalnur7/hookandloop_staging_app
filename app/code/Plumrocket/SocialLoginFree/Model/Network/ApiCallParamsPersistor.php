<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Psr\Log\LoggerInterface;

class ApiCallParamsPersistor implements ApiCallParamsPersistorInterface
{

    public const PSLOGIN_SESSION_PART_NAME = 'pslogin_api_call_params';
    public const PERSISTENS_COOKIE_NAME = 'pr_social_login_persist';

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @param \Magento\Customer\Model\Session                        $customerSession
     * @param \Magento\Framework\Stdlib\CookieManagerInterface       $cookieManager
     * @param \Psr\Log\LoggerInterface                               $logger
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        CustomerSession $customerSession,
        CookieManagerInterface $cookieManager,
        LoggerInterface $logger,
        CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->customerSession = $customerSession;
        $this->cookieManager = $cookieManager;
        $this->logger = $logger;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * @inheritDoc
     */
    public function add(string $key, $value): ApiCallParamsPersistorInterface
    {
        $data = $this->customerSession->getData(self::PSLOGIN_SESSION_PART_NAME);
        $data[$key] = $value;
        $this->customerSession->setData(self::PSLOGIN_SESSION_PART_NAME, $data);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function set(array $value): ApiCallParamsPersistorInterface
    {
        $this->customerSession->setData(self::PSLOGIN_SESSION_PART_NAME, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key = null)
    {
        if (null !== $key) {
            $data = $this->customerSession->getData(self::PSLOGIN_SESSION_PART_NAME);
            return $data[$key] ?? null;
        }
        return $this->customerSession->getData(self::PSLOGIN_SESSION_PART_NAME);
    }

    /**
     * @inheritDoc
     */
    public function addQuoteId(int $quoteId): ApiCallParamsPersistorInterface
    {
        if (! $quoteId) {
            return $this;
        }

        $cookieMetadata = $this->cookieMetadataFactory->createSensitiveCookieMetadata()->setPath('/');
        try {
            // todo: add mask
            $this->cookieManager->setSensitiveCookie(self::PERSISTENS_COOKIE_NAME, $quoteId, $cookieMetadata);
        } catch (InputException|CookieSizeLimitReachedException|FailureToSendException $e) {
            $this->logger->debug($e->getMessage());
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQuoteId(): int
    {
        return (int) $this->cookieManager->getCookie(self::PERSISTENS_COOKIE_NAME);
    }

    /**
     * @inheritDoc
     */
    public function clear(): ApiCallParamsPersistorInterface
    {
        $this->customerSession->unsetData(self::PSLOGIN_SESSION_PART_NAME);
        try {
            $this->cookieManager->deleteCookie(self::PERSISTENS_COOKIE_NAME);
        } catch (InputException|FailureToSendException $e) {
            $this->logger->debug($e->getMessage());
        }
        return $this;
    }
}
