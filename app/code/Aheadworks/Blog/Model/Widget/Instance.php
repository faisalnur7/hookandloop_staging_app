<?php
namespace Aheadworks\Blog\Model\Widget;

use Magento\Widget\Model\Widget\Instance as NativeInstance;
use Aheadworks\Blog\Block\Adminhtml\Widget\Instance\Grid\AbstractGrid;

/**
 * Class Instance
 */
class Instance extends NativeInstance
{
    const GRID_DISPLAY_TYPE = 'grid';

    /**
     * @var array
     */
    private $chooserBlocks = [];

    /**
     * @var array
     */
    private $errors = [];

    /**
     * Internal Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_layoutHandles = array_merge($this->_layoutHandles, $this->getLayoutHandles());
        $this->_specificEntitiesLayoutHandles = array_merge($this->_specificEntitiesLayoutHandles, $this->getSpecificEntitiesLayoutHandles());
    }

    /**
     * Retrieve display on options data
     *
     * @return array
     */
    public function getDisplayOnOptionsData()
    {
        return $this->getDisplayOnOptions();
    }

    /**
     * Retrieve display on containers data
     *
     * @return array
     */
    public function getDisplayOnContainersData()
    {
        return $this->getDisplayOnContainers();
    }

    /**
     * Retrieve Chooser Block By Type
     *
     * @param string $type
     * @param string $displayType
     * @return bool|\Magento\Framework\Phrase
     */
    public function getChooserBlockByType($type, $displayType)
    {
        $this->chooserBlocks = $this->getChooserBlocks();
        $chooserBlock = $this->getBlock($type, $displayType);

        if ($displayType === self::GRID_DISPLAY_TYPE && !$this->checkGridTypeInheritance($chooserBlock)) {
            $this->errors[] = 'Chooser block by grid display type must implement ' . AbstractGrid::class;
        }

        return $chooserBlock;
    }

    /**
     * Retrieve Block
     *
     * @param string $type
     * @param string $displayType
     * @return bool
     */
    private function getBlock($type, $displayType)
    {
        $chooserBlock = $this->chooserBlocks[$displayType][$type] ?? null;

        if (!$chooserBlock) {
            $this->errors[] = sprintf('Chooser block unavailable: %s', $type);
        }

        return $chooserBlock;
    }

    /**
     * Check Is Grid Type Inheritance
     *
     * @param string $type
     * @return bool
     */
    private function checkGridTypeInheritance($chooserBlock)
    {
        return $chooserBlock instanceof AbstractGrid;
    }

    /**
     * Retrieve Errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}