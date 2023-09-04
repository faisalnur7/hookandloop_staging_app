<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network;

use Magento\Store\Model\Store;
use Plumrocket\SocialLoginFree\Api\Data\ButtonInterface;
use Plumrocket\SocialLoginFree\Api\NetworkButtonResolverInterface;
use Plumrocket\SocialLoginFree\Helper\Config\Button;
use Plumrocket\SocialLoginFree\Helper\Config\Network as NetworkConfig;
use Plumrocket\SocialLoginFree\Model\Network\Data\ButtonFactory;

/**
 * @since 4.0.0
 */
class DefaultButtonsResolver implements NetworkButtonResolverInterface
{
    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Network
     */
    private $networkConfig;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Button
     */
    private $buttonConfig;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\Data\ButtonFactory
     */
    private $buttonFactory;

    /**
     * @param \Magento\Store\Model\Store                                   $store
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Network            $networkConfig
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Button             $buttonConfig
     * @param \Plumrocket\SocialLoginFree\Model\Network\Data\ButtonFactory $buttonFactory
     */
    public function __construct(
        Store $store,
        NetworkConfig $networkConfig,
        Button $buttonConfig,
        ButtonFactory $buttonFactory
    ) {
        $this->store = $store;
        $this->networkConfig = $networkConfig;
        $this->buttonConfig = $buttonConfig;
        $this->buttonFactory = $buttonFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(string $networkCode): ButtonInterface
    {
        /** @var \Plumrocket\SocialLoginFree\Api\Data\ButtonInterface $button */
        $button = $this->buttonFactory->create();
        $button->setNetworkCode($networkCode);
        $button->setButtonResolver($this);
        $button->setData('image', ['icon' => null, 'login' => null, 'register' => null]);

        $button->setDesign($this->buttonConfig->getDesign());

        $button->setLoginText($this->buttonConfig->getLoginBtnText($networkCode));
        $button->setRegisterText($this->buttonConfig->getRegisterBtnText($networkCode));

        $button->setModalWidth($this->getModalWidth($networkCode));
        $button->setModalHeight($this->getModalHeight($networkCode));

        if ($this->networkConfig->getApplicationId($networkCode)
            && $this->networkConfig->getApplicationSecretKey($networkCode)
        ) {
            $button->setUrl($this->getUrl($networkCode, ''));
            $button->setLoginUrl($this->resolveLoginUrl($networkCode));
            $button->setRegisterUrl($this->resolveRegistrationUrl($networkCode));
        } else {
            $button->setUrl('');
            $button->setLoginUrl('');
            $button->setRegisterUrl('');
        }

        return $button;
    }

    /**
     * Resolve login url.
     *
     * @param string $networkCode
     * @param array  $params
     * @return string
     */
    public function resolveLoginUrl(string $networkCode, array $params = []): string
    {
        return $this->getUrl($networkCode, ButtonInterface::CUSTOMER_ACTION_LOGIN, $params);
    }

    /**
     * Resolve registration url.
     *
     * @param string $networkCode
     * @param array  $params
     * @return string
     */
    public function resolveRegistrationUrl(string $networkCode, array $params = []): string
    {
        return $this->getUrl($networkCode, ButtonInterface::CUSTOMER_ACTION_REGISTER, $params);
    }

    /**
     * Get button Url
     *
     * @param string $networkCode
     * @param string $customerAction
     * @param array  $params
     * @return string
     */
    private function getUrl(string $networkCode, string $customerAction, array $params = []): string
    {
        $uriParams = [
            'type' => $networkCode,
            'refresh' => time()
        ];

        if (! empty($customerAction)) {
            $uriParams['customer_action'] = $customerAction;
        }

        return $this->store->getUrl('pslogin/account/douse', array_merge($uriParams, $params));
    }

    /**
     * Get login modal width.
     *
     * @param string $networkCode
     * @return int
     */
    private function getModalWidth(string $networkCode): int
    {
        $modalWidth = $this->networkConfig->getModalWidth($networkCode);
        if (! empty($modalWidth)) {
            return $modalWidth;
        }
        return 650;
    }

    /**
     * Get login modal height.
     *
     * @param string $networkCode
     * @return int
     */
    private function getModalHeight(string $networkCode): int
    {
        $modalWidth = $this->networkConfig->getModalHeight($networkCode);
        if (! empty($modalWidth)) {
            return $modalWidth;
        }
        return 550;
    }
}
