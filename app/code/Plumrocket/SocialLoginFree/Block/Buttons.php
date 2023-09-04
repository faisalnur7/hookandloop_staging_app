<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Block;

use Plumrocket\SocialLoginFree\Model\OptionSource\ButtonDesign;

/**
 * Can receive following params:
 *
 * position - set if buttons is going to be rendered for login or registration form
 * custom_design - change buttons to giving design
 */
class Buttons extends \Magento\Framework\View\Element\Template
{

    /**
     * @var bool
     */
    private $_output2js = false;

    /**
     * @var null
     * @deprecated since 4.0.0
     */
    private $_checkPosition = null;

    /**
     * @var \Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface
     */
    private $buttonsProvider;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Button
     */
    private $buttonConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context               $context
     * @param \Plumrocket\SocialLoginFree\Helper\Data                        $dataHelper
     * @param \Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface $buttonsProvider
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Button               $buttonConfig
     * @param array                                                          $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Plumrocket\SocialLoginFree\Helper\Data $dataHelper,
        \Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface $buttonsProvider,
        \Plumrocket\SocialLoginFree\Helper\Config\Button $buttonConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->buttonsProvider = $buttonsProvider;
        $this->buttonConfig = $buttonConfig;
    }

    /**
     * Get position of rendering.
     *
     * @return string
     */
    public function getPosition(): string
    {
        return (string) $this->_getData('position') ?: (string) $this->_checkPosition;
    }

    /**
     * Get position of rendering.
     *
     * @param string $position
     * @return \Plumrocket\SocialLoginFree\Block\Buttons
     */
    public function setPosition(string $position): self
    {
        return $this->setData('position', $position);
    }

    /**
     * Get social buttons.
     *
     * @return \Plumrocket\SocialLoginFree\Api\Data\ButtonInterface[]
     */
    public function getButtons(): array
    {
        return $this->buttonsProvider->getDefaultList();
    }

    /**
     * Get custom design.
     *
     * @return string
     */
    public function getCustomDesign(): string
    {
        return (string) $this->getData('custom_design');
    }

    /**
     * Set custom design.
     *
     * @param string $customDesign
     * @return $this
     */
    public function setCustomDesign(string $customDesign): self
    {
        return $this->setData('custom_design', $customDesign);
    }

    /**
     * Get design of buttons.
     *
     * @return string
     */
    public function getButtonDesign(): string
    {
        if ($this->getCustomDesign()) {
            return $this->getCustomDesign();
        }

        $buttons = $this->getButtons();
        if (! $buttons) {
            return ButtonDesign::TYPE_DEFAULT;
        }

        return array_values($buttons)[0]->getDesign();
    }

    /**
     * @return \Plumrocket\SocialLoginFree\Api\Data\ButtonInterface[]
     * @deprecated since 4.0.0
     * @see getButtons
     */
    public function getPreparedButtons(): array
    {
        return $this->getButtons();
    }

    /**
     * @return bool
     * @deprecated since 4.0.0
     * @see getButtons
     */
    public function hasButtons()
    {
        return (bool) $this->getButtons();
    }

    /**
     * @return bool
     * @deprecated since 4.0.0
     * @see getCustomDesign()
     */
    public function showShortButtons(): bool
    {
        return (bool) $this->getData('show_short_buttons');
    }

    /**
     * @return bool
     * @deprecated since 4.0.0
     */
    public function showFullButtons(): bool
    {
        return ! $this->showShortButtons();
    }

    /**
     * @param $flag
     * @return void
     * @deprecated since 4.0.0
     */
    public function setOutput2js($flag = true)
    {
        $this->_output2js = (bool)$flag;
    }

    /**
     * @param $position
     * @return void
     * @deprecated since 4.0.0
     */
    public function checkPosition($position = null)
    {
        $this->_checkPosition = $position;
    }

    /**
     * Disable render is position is disabled.
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        if ($this->getPosition() && ! $this->buttonConfig->canDisplayOn($this->getPosition())) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * @param $html
     * @deprecated since 4.0.0
     */
    public function _afterToHtml($html)
    {
        if ($this->_output2js && trim($html)) {
            $html = '<script>'
                . 'window.psloginButtons = \''
                . str_replace(["\n", 'script'], ['', "scri'+'pt"], $this->escapeJs($html)) . '\';'
                . '</script>';
        }

        return parent::_afterToHtml($html);
    }
}
