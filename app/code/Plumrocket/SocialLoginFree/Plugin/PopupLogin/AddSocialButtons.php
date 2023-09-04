<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Plugin\PopupLogin;

use Magento\Framework\View\Element\BlockInterface;
use Plumrocket\Popuplogin\Block\Popuplogin\Social;
use Plumrocket\SocialLoginFree\Block\Buttons;
use Plumrocket\SocialLoginFree\Helper\Config;
use Plumrocket\SocialLoginFree\Helper\Config\PopupLoginConfig;
use Plumrocket\SocialLoginFree\Model\OptionSource\Position;

/**
 * Integration with Plumrocket Popup Login Extension.
 *
 * @since 4.0.0
 */
class AddSocialButtons
{

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\PopupLoginConfig
     */
    private $popupLoginConfig;

    /**
     * @param \Plumrocket\SocialLoginFree\Helper\Config                  $config
     * @param \Plumrocket\SocialLoginFree\Helper\Config\PopupLoginConfig $popupLoginConfig
     */
    public function __construct(Config $config, PopupLoginConfig $popupLoginConfig)
    {
        $this->config = $config;
        $this->popupLoginConfig = $popupLoginConfig;
    }

    /**
     * Set social buttons.
     *
     * @param \Plumrocket\Popuplogin\Block\Popuplogin\Social      $subject
     * @param \Magento\Framework\View\Element\BlockInterface|null $result
     * @return \Magento\Framework\View\Element\BlockInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetLoginButtonsBlock(Social $subject, ?BlockInterface $result): ?BlockInterface
    {
        if ($result || ! $this->config->isModuleEnabled()) {
            return $result;
        }

        $block = $subject->getLayout()->createBlock(Buttons::class);
        $block->setTemplate('Plumrocket_SocialLoginFree::customer/form/login/buttons.phtml');
        $block->setPosition(Position::BUTTONS_POSITION_LOGIN);
        $block->setCustomDesign($this->popupLoginConfig->getCustomLoginDesign());
        return $block;
    }

    /**
     * Set social buttons.
     *
     * @param \Plumrocket\Popuplogin\Block\Popuplogin\Social      $subject
     * @param \Magento\Framework\View\Element\BlockInterface|null $result
     * @return \Magento\Framework\View\Element\BlockInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetRegistrationButtonsBlock(Social $subject, ?BlockInterface $result): ?BlockInterface
    {
        if ($result || ! $this->config->isModuleEnabled()) {
            return $result;
        }

        $block = $subject->getLayout()->createBlock(Buttons::class);
        $block->setTemplate('Plumrocket_SocialLoginFree::customer/form/register/buttons.phtml');
        $block->setPosition(Position::BUTTONS_POSITION_REGISTER);
        $block->setCustomDesign($this->popupLoginConfig->getCustomRegistrationDesign());
        return $block;
    }
}
