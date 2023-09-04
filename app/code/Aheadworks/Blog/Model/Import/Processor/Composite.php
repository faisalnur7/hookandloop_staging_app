<?php
namespace Aheadworks\Blog\Model\Import\Processor;

/**
 * Class Composite
 */
class Composite implements ImportProcessorInterface
{
    /**
     * @var ImportProcessorInterface[]
     */
    private $processors;

    /**
     * @var array
     */
    private $defaultImportConfig;

    /**
     * Composite constructor.
     * @param array $processors
     * @param array $defaultImportConfig
     */
    public function __construct(
        array $processors = [],
        array $defaultImportConfig = []
    ) {
        $this->processors = $processors;
        $this->defaultImportConfig = $defaultImportConfig;
    }

    /**
     * @inheritDoc
     */
    public function perform($data)
    {
        $result = false;
        $type = $data['entity'] ?? null;

        if ($this->isAllowedRunProcessor($type)) {
            $data = array_merge($data, $this->defaultImportConfig);
            $result = $this->processors[$type]->perform($data);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function saveEntity($rowData, $type = null)
    {
        $result = false;

        if ($this->isAllowedRunProcessor($type)) {
            $result = $this->processors[$type]->saveEntity($rowData);
        }

        return $result;
    }

    /**
     * Check is exist processor
     *
     * @param string $type
     * @return bool
     */
    private function isExistProcessor($type)
    {
        return $type && isset($this->processors[$type]);
    }

    /**
     * Check is allowed run processor
     *
     * @param string $type
     * @return bool
     */
    private function isAllowedRunProcessor($type)
    {
        return $this->isExistProcessor($type) && $this->processors[$type] instanceof ImportProcessorInterface;
    }
}