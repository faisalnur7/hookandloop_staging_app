<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Block;

class General extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Plumrocket\SocialLoginFree\Helper\Config        $config
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Plumrocket\SocialLoginFree\Helper\Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->serializer = $serializer;
        $this->config = $config;
    }

    /**
     * @return string|void
     */
    protected function _toHtml()
    {
        if (!$this->config->isModuleEnabled()) {
            return;
        }

        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getSkipModules()
    {
        $skipModules = ['customer/account', 'pslogin/account'];
        return $this->serializer->serialize($skipModules);
    }
}
