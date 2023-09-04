<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Plumrocket\Base\Model\IsModuleInMarketplace;
use Plumrocket\SocialLoginFree\Model\ProIntegrations;

/**
 * @since 4.0.0
 */
class ProAd extends Field
{

    /**
     * @var string
     */
    protected $_template = 'Plumrocket_SocialLoginFree::pro_ad.phtml';

    /**
     * @var \Plumrocket\Base\Model\IsModuleInMarketplace
     */
    private $isModuleInMarketplace;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\ProIntegrations
     */
    private $proIntegrations;

    /**
     * @param \Magento\Backend\Block\Template\Context           $context
     * @param \Plumrocket\Base\Model\IsModuleInMarketplace      $isModuleInMarketplace
     * @param \Plumrocket\SocialLoginFree\Model\ProIntegrations $proIntegrations
     * @param array                                             $data
     */
    public function __construct(
        Context $context,
        IsModuleInMarketplace $isModuleInMarketplace,
        ProIntegrations $proIntegrations,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->isModuleInMarketplace = $isModuleInMarketplace;
        $this->proIntegrations = $proIntegrations;
    }

    /**
     * Render promo banner
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderValue(AbstractElement $element): string
    {
        return $this->toHtml();
    }

    /**
     * Get extension url.
     *
     * @return string
     */
    public function getProExtensionUrl(): string
    {
        return $this->isModuleInMarketplace->execute('SocialLoginFree')
            ? 'https://marketplace.magento.com/plumrocket-module-psloginpro.html'
            : 'https://plumrocket.com/magento-social-login-pro';
    }

    /**
     * Disable inheritance checkbox
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return bool
     */
    protected function _isInheritCheckboxRequired(AbstractElement $element): bool
    {
        return false;
    }

    /**
     * Disable scope label rendering
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderScopeLabel(AbstractElement $element): string
    {
        return '';
    }

    /**
     * Get integration from Pro version.
     *
     * @return \string[][]
     */
    public function getIntegrations(): array
    {
        $integrations = [];
        foreach ($this->proIntegrations->execute() as $integration) {
            $integrations[] = [
                'name' => $integration['name'],
                'iconUrl' => $integration['src'],
            ];
        }
        return $integrations;
    }
}
