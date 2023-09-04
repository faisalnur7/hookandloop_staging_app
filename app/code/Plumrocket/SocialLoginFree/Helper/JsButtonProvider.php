<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface;
use Plumrocket\SocialLoginFree\Helper\Config\Button;
use Plumrocket\SocialLoginFree\Model\OptionSource\Position;

/**
 * @since 4.0.0
 */
class JsButtonProvider extends AbstractHelper
{

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Button
     */
    private $buttonConfig;

    /**
     * @var \Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface
     */
    private $networkButtonProvider;

    /**
     * @param \Magento\Framework\App\Helper\Context                          $context
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Button               $buttonConfig
     * @param \Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface $networkButtonProvider
     */
    public function __construct(
        Context $context,
        Button $buttonConfig,
        NetworkButtonProviderInterface $networkButtonProvider
    ) {
        parent::__construct($context);
        $this->buttonConfig = $buttonConfig;
        $this->networkButtonProvider = $networkButtonProvider;
    }

    /**
     * Get configurations for buttons.
     *
     * @return array
     */
    public function getAuthenticationPopupButtons(): array
    {
        if (! $this->buttonConfig->canDisplayOn(Position::BUTTONS_POSITION_LOGIN)) {
            return [];
        }

        $buttons = [];
        foreach ($this->networkButtonProvider->getDefaultList() as $button) {
            $buttons[] = [
                'networkCode' => $button->getNetworkCode(),
                'design' => $button->getDesign(),
                'text' => $button->getLoginText(),
                'url' => $button->getLoginUrl(['pr_var_redirect_to' => 'checkout']),
                'modalHeight' => $button->getModalHeight(),
                'modalWidth' => $button->getModalWidth(),
                'openOnPage' => $button->shouldOpenOnPage(),
            ];
        }
        return $buttons;
    }

    /**
     * Get configurations for login buttons.
     *
     * @return array
     */
    public function getLoginButtons(): array
    {
        if (! $this->buttonConfig->canDisplayOn(Position::BUTTONS_POSITION_LOGIN)) {
            return [];
        }

        $buttons = [];
        foreach ($this->networkButtonProvider->getDefaultList() as $button) {
            $buttons[] = [
                'networkCode' => $button->getNetworkCode(),
                'design' => $button->getDesign(),
                'text' => $button->getLoginText(),
                'url' => $button->getLoginUrl(),
                'modalHeight' => $button->getModalHeight(),
                'modalWidth' => $button->getModalWidth(),
                'openOnPage' => $button->shouldOpenOnPage(),
            ];
        }
        return $buttons;
    }

    /**
     * Get configurations for registration buttons.
     *
     * @return array
     */
    public function getRegistrationButtons(): array
    {
        if (! $this->buttonConfig->canDisplayOn(Position::BUTTONS_POSITION_REGISTER)) {
            return [];
        }

        $buttons = [];
        foreach ($this->networkButtonProvider->getDefaultList() as $button) {
            $buttons[] = [
                'networkCode' => $button->getNetworkCode(),
                'design' => $button->getDesign(),
                'text' => $button->getRegisterText(),
                'url' => $button->getRegisterUrl(),
                'modalHeight' => $button->getModalHeight(),
                'modalWidth' => $button->getModalWidth(),
                'openOnPage' => $button->shouldOpenOnPage(),
            ];
        }
        return $buttons;
    }
}
