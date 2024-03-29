<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Controller\Adminhtml\Ebay;

abstract class Main extends \Ess\M2ePro\Controller\Adminhtml\Main
{
    /** @var \Ess\M2ePro\Model\ActiveRecord\Component\Parent\Ebay\Factory */
    protected $ebayFactory;

    public function __construct(
        \Ess\M2ePro\Model\ActiveRecord\Component\Parent\Ebay\Factory $ebayFactory,
        \Ess\M2ePro\Controller\Adminhtml\Context $context
    ) {
        $this->ebayFactory = $ebayFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ess_M2ePro::ebay');
    }

    protected function getCustomViewNick()
    {
        return \Ess\M2ePro\Helper\View\Ebay::NICK;
    }

    protected function initResultPage()
    {
        if ($this->resultPage !== null) {
            return;
        }

        parent::initResultPage();

        $this->getResultPage()->getConfig()->getTitle()->prepend(
            $this->getHelper('View\Ebay')->getTitle()
        );

        if ($this->getLayoutType() != self::LAYOUT_BLANK) {
            $this->getResultPage()->setActiveMenu(\Ess\M2ePro\Helper\View\Ebay::MENU_ROOT_NODE_NICK);
        }
    }
}
