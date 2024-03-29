<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Block\Adminhtml\Listing\View;

abstract class Switcher extends \Ess\M2ePro\Block\Adminhtml\Switcher
{
    protected $paramName = 'view_mode';
    protected $viewMode = null;

    /** @var \Ess\M2ePro\Helper\Data\Session */
    protected $sessionDataHelper;

    public function __construct(
        \Ess\M2ePro\Helper\Data\Session $sessionDataHelper,
        \Ess\M2ePro\Block\Adminhtml\Magento\Context\Template $context,
        array $data = []
    ) {
        $this->sessionDataHelper = $sessionDataHelper;
        parent::__construct($context, $data);
    }

    abstract protected function getComponentMode();

    abstract protected function getDefaultViewMode();

    public function getLabel()
    {
        return $this->__('View Mode');
    }

    public function hasDefaultOption()
    {
        return false;
    }

    public function getStyle()
    {
        return self::ADVANCED_STYLE;
    }

    public function getDefaultParam()
    {
        $listing = $this->activeRecordFactory->getCachedObjectLoaded(
            'Listing',
            $this->getRequest()->getParam('id')
        );

        $sessionViewMode = $this->sessionDataHelper->getValue(
            "{$this->getComponentMode()}_listing_{$listing->getId()}_view_mode"
        );

        if ($sessionViewMode === null) {
            return $this->getDefaultViewMode();
        }

        return $sessionViewMode;
    }

    public function getSelectedParam()
    {
        if ($this->viewMode !== null) {
            return $this->viewMode;
        }

        $selectedViewMode = parent::getSelectedParam();

        $listing = $this->activeRecordFactory->getCachedObjectLoaded(
            'Listing',
            $this->getRequest()->getParam('id')
        );

        $this->sessionDataHelper->setValue(
            "{$this->getComponentMode()}_listing_{$listing->getId()}_view_mode",
            $selectedViewMode
        );

        $this->viewMode = $selectedViewMode;

        return $this->viewMode;
    }
}
