<?php
namespace Aheadworks\Helpdesk2\Plugin\Wysiwyg\Model;

use Magento\PageBuilder\Model\Wysiwyg\DefaultConfigProvider;
use Magento\Framework\DataObject;

/**
 * Class DefaultConfigProviderPlugin
 *
 * @package Aheadworks\Helpdesk2\Plugin\Wysiwyg\Model
 */
class DefaultConfigProviderPlugin
{
    const TINY_FIELD = 'tinymce';

    /**
     * Modify tiny config provider behaviour. Implement value setting based on merge.
     *
     * @param DefaultConfigProvider $subject
     * @param callable $proceed
     * @param DataObject $config
     * @return DataObject
     */
    public function aroundGetConfig(DefaultConfigProvider $subject, callable $proceed, DataObject $config)
    {
        $configFromXml = $this->getData($config);
        $config = $proceed($config);
        $defaultConfig = $this->getData($config);

        if (!empty($configFromXml)) {
            $mergedConfig = array_replace_recursive($defaultConfig, $configFromXml);
            $config->setData(self::TINY_FIELD, $mergedConfig);
        }

        return $config;
    }

    /**
     * Retrieve tinyMce config
     *
     * @param DataObject $config
     * @return array|mixed
     */
    private function getData(DataObject $config)
    {
        return $config->hasData(self::TINY_FIELD)
            ? $config->getData(self::TINY_FIELD)
            : [];
    }
}
