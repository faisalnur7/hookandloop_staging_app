<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/ End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Controller\FacebookDeletion;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\SocialLoginFree\Helper\Config\Network;
use Plumrocket\SocialLoginFree\Model\DataPrivacy\Facebook\AutomaticDeletionRequestManager;
use Magento\Framework\UrlInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * @since 3.1.0
 */
class Request implements CsrfAwareActionInterface, HttpPostActionInterface
{
    /**
     * @var \Plumrocket\SocialLoginFree\Model\DataPrivacy\Facebook\AutomaticDeletionRequestManager
     */
    private $deletionRequestManager;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    private $resultFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var StoreManagerInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Network
     */
    private $networkConfig;

    /**
     * @param \Plumrocket\SocialLoginFree\Model\DataPrivacy\Facebook\AutomaticDeletionRequestManager $deletionRequestManager
     * @param \Magento\Framework\Controller\ResultFactory                                            $resultFactory
     * @param \Magento\Framework\App\RequestInterface                                                $request
     * @param \Magento\Framework\UrlInterface                                                        $urlBuilder
     * @param \Magento\Framework\Serialize\SerializerInterface                                       $serializer
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Network                                      $networkConfig
     */
    public function __construct(
        AutomaticDeletionRequestManager $deletionRequestManager,
        ResultFactory $resultFactory,
        RequestInterface $request,
        UrlInterface $urlBuilder,
        SerializerInterface $serializer,
        Network $networkConfig
    ) {
        $this->deletionRequestManager = $deletionRequestManager;
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->serializer = $serializer;
        $this->networkConfig = $networkConfig;
    }

    /**
     * @throws \Magento\Framework\Exception\AlreadyExistsException|\Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $signedRequest = $this->request->getParam('signed_request');

        $userId = $this->getUserId($signedRequest);
        $confirmationCode = $this->deletionRequestManager->register($userId)->getConfirmationCode();

        $returnJsonData = [
            'url' => $this->urlBuilder->getStore()->getUrl(
                'pslogin/facebookdeletion/status',
                ['confirmationCode' => $confirmationCode]
            ),
            'confirmation_code' => $confirmationCode
        ];

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($returnJsonData);
    }

    /**
     * @param string $signedRequest
     * @return string
     */
    private function getUserId(string $signedRequest): string
    {
        [, $payload] = explode('.', $signedRequest, 2);
        return (string) $this->serializer->unserialize($this->base64UrlDecode($payload))['user_id'];
    }

    /**
     * @param string $input
     * @return string
     */
    private function base64UrlDecode(string $input): string
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return InvalidRequestException
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool|null
     * @throws \Exception
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        if (null === $request->getParam('signed_request')) {
            return false;
        }

        [$encodeSig, $payload] = explode('.', $request->getParam('signed_request'), 2);

        $secret = $this->networkConfig->getApplicationSecretKey('facebook');

        $sig = $this->base64UrlDecode($encodeSig);
        $expectedSig = hash_hmac('sha256', $payload, $secret, true);

        return $sig === $expectedSig;
    }
}
