<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Setup\SampleData\Executor as SampleDataExecutor;
use Aheadworks\Helpdesk2\Model\SampleData\Installer as SampleDataInstaller;

class InstallSampleData implements DataPatchInterface, PatchRevertableInterface, PatchVersionInterface
{
    /**
     * @param SampleDataExecutor $sampleDataExecutor
     * @param SampleDataInstaller $sampleDataInstaller
     */
    public function __construct(
        private readonly SampleDataExecutor $sampleDataExecutor,
        private readonly SampleDataInstaller $sampleDataInstaller
    ) {
    }

    /**
     * Install sample data
     */
    public function apply()
    {
        $this->sampleDataExecutor->exec($this->sampleDataInstaller);

        return $this;
    }

    /**
     * Remove patch on uninstall command in order to be able to install it again
     */
    public function revert()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getVersion()
    {
        return '2.0.0';
    }
}
