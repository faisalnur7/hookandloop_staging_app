<?php
namespace Aheadworks\Blog\Model\Export;

/**
 * Class ConfigEntity
 */
class ConfigProvider
{
    /**
     * @var array
     */
    private $config;

    /**
     * ConfigProvider constructor.
     * @param array $config
     */
    public function __construct(
        $config = []
    ) {
        $this->config = $config;
    }

    /**
     * Retrieve export configuration
     *
     * @return array
     */
    public function getExportConfig()
    {
        return $this->config;
    }
}