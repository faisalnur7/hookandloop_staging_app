<?php
/**
 * Copyright © Pronko Consulting (https://www.pronkoconsulting.com)
 * See LICENSE for the license details.
 */
namespace Pronko\Bing\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Pronko\Bing\Api\ConfigInterface;

class Tag extends Template
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * Tag constructor.
     * @param Context $context
     * @param ConfigInterface $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigInterface $config,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getTagId()
    {
        return $this->config->getTagId();
    }
}
