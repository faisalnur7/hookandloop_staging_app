<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Block;

use Plumrocket\SocialLoginFree\Helper\Config\SharePopup;

class Share extends \Magento\Framework\View\Element\Template
{
    /**
     * @var array
     */
    protected $buttonTypes = [
        'facebook',
        'twitter',
        'linkedin'         => 'LinkedIn',
        'pinterest',
        'amazonwishlist'   => 'Amazon',
        'vk'               => 'Vkontakte',
        'odnoklassniki_ru' => 'Odnoklassniki',
        'mymailru'         => 'Mail',
        'blogger',
        'delicious',
        'wordpress',
        'email',
        'addthis'          => 'AddThis',
    ];

    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;

    /**
     * @var \Magento\Cms\Helper\Page
     */
    private $page;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    private $filterProvider;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\SharePopup
     */
    private $sharePopupConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context     $context
     * @param \Magento\Store\Model\Store                           $store
     * @param \Magento\Cms\Helper\Page                             $page
     * @param \Magento\Cms\Model\Template\FilterProvider           $filterProvider
     * @param \Plumrocket\SocialLoginFree\Helper\Config\SharePopup $sharePopupConfig
     * @param array                                                $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\Store $store,
        \Magento\Cms\Helper\Page $page,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        SharePopup $sharePopupConfig,
        array $data = []
    ) {
        $this->store = $store;
        $this->page = $page;
        $this->filterProvider = $filterProvider;
        parent::__construct($context, $data);
        $this->sharePopupConfig = $sharePopupConfig;
    }

    /**
     * Check if share popup is enabled.
     *
     * @return bool
     */
    public function showPopup(): bool
    {
        return $this->sharePopupConfig->isEnabled();
    }

    /**
     * Get share buttons types.
     *
     * @return array
     */
    public function getButtonTypes(): array
    {
        if (! $this->hasData('button_types')) {
            $this->setData('button_types', (array) $this->buttonTypes);
        }
        return $this->getData('button_types');
    }

    /**
     * Get share buttons.
     *
     * @return array
     */
    public function getButtons(): array
    {
        $buttons = [];
        foreach ($this->getButtonTypes() as $key1 => $key2) {
            $key = (!is_numeric($key1)) ? $key1 : $key2;
            $title = ucfirst($key2);
            $buttons[] = ['key' => $key, 'title' => $title];
        }
        return $buttons;
    }

    /**
     * Get page url.
     *
     * @return string
     */
    public function getPageUrl(): string
    {
        $page = $this->sharePopupConfig->getPage();
        switch ($page) {
            case '__custom__':
                $pageUrl = $this->sharePopupConfig->getCustomPageUrl();
                if (stripos($pageUrl, 'http') !== 0) {
                    $pageUrl = $this->store->getBaseUrl() . $pageUrl;
                }
                break;
            default:
                if (is_numeric($page)) {
                    $pageUrl = $this->page->getPageUrl($page);
                } else {
                    $pageUrl = '';
                }
        }

        // Disable addthis analytics anchor.
        $pageUrl .= '#';
        return $pageUrl;
    }

    /**
     * Add params to js layout.
     *
     * @return string
     */
    public function getJsLayout(): string
    {
        if ($this->jsLayout) {
            $config = [
                'title' => $this->sharePopupConfig->getTitle(),
                'description' => $this->escapeHtml(
                    $this->filterProvider->getPageFilter()->filter(
                        $this->sharePopupConfig->getDescription()
                    )
                ),
                'url' => $this->getPageUrl(),
                'buttons' => $this->getButtons(),
            ];
            $this->jsLayout['components']['pslogin-sharepopup']['config'] = array_merge(
                $this->jsLayout['components']['pslogin-sharepopup']['config'],
                $config
            );
        }
        return parent::getJsLayout();
    }
}
