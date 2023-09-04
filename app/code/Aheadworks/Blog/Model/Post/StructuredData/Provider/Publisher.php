<?php
namespace Aheadworks\Blog\Model\Post\StructuredData\Provider;

use Aheadworks\Blog\Model\Post\StructuredData\ProviderInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Theme\Block\Html\Header\Logo as HeaderLogoBlock;
use Aheadworks\Blog\Model\Config;

/**
 * Class Publisher
 *
 * @package Aheadworks\Blog\Model\Post\StructuredData\Provider
 */
class Publisher implements ProviderInterface
{
    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param LayoutInterface $layout
     * @param Config $config
     */
    public function __construct(
        LayoutInterface $layout,
        Config $config
    ) {
        $this->layout = $layout;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($post)
    {
        $data = [];

        $organizationName = $this->config->getOrganizationName();
        if (!empty($organizationName)) {
            $publisherData = [
                "@type" => "Organization",
                "name" => $organizationName,
            ];
            $logoBlock = $this->layout->getBlock('logo');
            if ($logoBlock instanceof HeaderLogoBlock) {
                $publisherData["logo"] = [
                    "@type" => "ImageObject",
                    "url" => $logoBlock->getLogoSrc(),
                ];
            }

            $data["publisher"] = $publisherData;
        }

        return $data;
    }
}
