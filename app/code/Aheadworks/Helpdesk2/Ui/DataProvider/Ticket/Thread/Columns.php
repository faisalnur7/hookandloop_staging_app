<?php
namespace Aheadworks\Helpdesk2\Ui\DataProvider\Ticket\Thread;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns as ListingColumns;
use Aheadworks\Helpdesk2\Api\ThreadColumnModifierInterface;
use Aheadworks\Helpdesk2\Model\ThirdPartyModule\ModuleChecker;
use Aheadworks\Helpdesk2\Model\Config;

class Columns extends ListingColumns
{
    const TEMPLATE_FOR_OLD_VERSION = 'Aheadworks_Helpdesk2/ui/form/element/ticket/thread/listing-divided-by-tabs';
    const TEMPLATE_FOR_NEW_VERSION = 'Aheadworks_Helpdesk2/ui/form/element/ticket/thread/listing-divided-by-tabs-new';

    /**
     * @var ModuleChecker
     */
    private $moduleChecker;

    /**
     * Columns constructor.
     * @param ContextInterface $context
     * @param ModuleChecker $moduleChecker
     * @param Config $config
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        ModuleChecker $moduleChecker,
        private Config $config,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->moduleChecker = $moduleChecker;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $data = $this->getData();
        if ($this->moduleChecker->isNewVersionMagento()) {
            $data['config']['listTemplate'] = self::TEMPLATE_FOR_NEW_VERSION;
        } else {
            $data['config']['listTemplate'] = self::TEMPLATE_FOR_OLD_VERSION;
        }

        if (isset($data['config']['tabs'])) {
            $defaultActiveTab = 'tab-' . $this->config->getTicketViewDefaultTab();
            $data['config']['defaultActiveTabIndex'] = $defaultActiveTab;
        }

        $this->setData($data);
        parent::prepare();
    }
}
