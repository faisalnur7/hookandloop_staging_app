<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Layout\Processor;

use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer\CreationRendererInterface;
use Aheadworks\Helpdesk2\Model\Config;

/**
 * Class Attachments
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout\Processor
 */
class Attachments implements ProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $configPath;

    /**
     * @param ArrayManager $arrayManager
     * @param Config $config
     * @param string $configPath
     */
    public function __construct(
        ArrayManager $arrayManager,
        Config $config,
        $configPath = 'components/aw_helpdesk2_form/children/general/children/attachments'
    ) {
        $this->arrayManager = $arrayManager;
        $this->config = $config;
        $this->configPath = $configPath;
    }

    /**
     * Prepare uploader element
     *
     * @param array $jsLayout
     * @param CreationRendererInterface $renderer
     * @return array
     */
    public function process($jsLayout, $renderer)
    {
        $uploaderConfig = [
            'maxFileSize' => false,
            'allowedExtensions' => $this->config->getAllowedFileExtensions($renderer->getStoreId()),
            'visible' => $this->config->isAllowedToAttachFiles($renderer->getStoreId())
        ];
        $maxFileSize = $this->config->getMaxUploadFileSize($renderer->getStoreId());
        if ($maxFileSize) {
            $uploaderConfig['maxFileSize'] = $maxFileSize;
        }

        $jsLayout = $this->arrayManager->merge(
            $this->configPath,
            $jsLayout,
            $uploaderConfig
        );

        return $jsLayout;
    }
}
