<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\Walmart\Marketplace\Issue;

use Ess\M2ePro\Helper\Json as JsonHelper;
use Ess\M2ePro\Model\Marketplace;

class NotUpdated implements \Ess\M2ePro\Model\Issue\LocatorInterface
{
    /** @var string */
    private const CACHE_KEY = __CLASS__;

    /** @var \Magento\Backend\Model\UrlInterface */
    private $urlBuilder;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resourceConnection;
    /** @var \Ess\M2ePro\Helper\View\Walmart */
    private $walmartViewHelper;
    /** @var \Ess\M2ePro\Helper\Data\Cache\Permanent */
    private $permanentCacheHelper;
    /** @var \Ess\M2ePro\Helper\Module\Database\Structure */
    private $moduleDatabaseStructureHelper;
    /** @var \Ess\M2ePro\Helper\Module\Translation */
    private $translationHelper;
    /** @var \Ess\M2ePro\Model\Issue\DataObjectFactory */
    private $issueFactory;
    /** @var \Ess\M2ePro\Model\MarketplaceFactory */
    private $marketplaceFactory;
    /** @var \Ess\M2ePro\Helper\Component\Walmart */
    private $walmartComponentHelper;

    public function __construct(
        \Magento\Backend\Model\UrlInterface $urlBuilder,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Ess\M2ePro\Helper\View\Walmart $walmartViewHelper,
        \Ess\M2ePro\Helper\Data\Cache\Permanent $permanentCacheHelper,
        \Ess\M2ePro\Helper\Module\Database\Structure $moduleDatabaseStructureHelper,
        \Ess\M2ePro\Helper\Module\Translation $translationHelper,
        \Ess\M2ePro\Model\Issue\DataObjectFactory $issueFactory,
        \Ess\M2ePro\Helper\Component\Walmart $walmartComponentHelper,
        \Ess\M2ePro\Model\MarketplaceFactory $marketplaceFactory
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->resourceConnection = $resourceConnection;
        $this->walmartViewHelper = $walmartViewHelper;
        $this->permanentCacheHelper = $permanentCacheHelper;
        $this->moduleDatabaseStructureHelper = $moduleDatabaseStructureHelper;
        $this->translationHelper = $translationHelper;
        $this->issueFactory = $issueFactory;
        $this->walmartComponentHelper = $walmartComponentHelper;
        $this->marketplaceFactory = $marketplaceFactory;
    }

    /**
     * @inheritDoc
     * @throws \Ess\M2ePro\Model\Exception\Logic
     * @throws \Zend_Db_Statement_Exception
     * @throws \Ess\M2ePro\Model\Exception
     */
    public function getIssues(): array
    {
        if (!$this->isNeedProcess()) {
            return [];
        }

        $outdatedMarketplaces = $this->permanentCacheHelper->getValue(self::CACHE_KEY);
        if (empty($outdatedMarketplaces)) {
            $tableName = $this->moduleDatabaseStructureHelper
                ->getTableNameWithPrefix('m2epro_walmart_dictionary_marketplace');

            $queryStmt = $this->resourceConnection
                ->getConnection()
                ->select()
                ->from(
                    $tableName,
                    ['marketplace_id', 'server_details_last_update_date']
                )
                ->where('client_details_last_update_date IS NOT NULL')
                ->where('server_details_last_update_date IS NOT NULL')
                ->where(
                    'client_details_last_update_date < server_details_last_update_date'
                )
                ->query();

            $dictionaryData = [];
            while ($row = $queryStmt->fetch()) {
                $dictionaryData[(int)$row['marketplace_id']] = $row['server_details_last_update_date'];
            }

            $marketplaceModel = $this->marketplaceFactory->create();

            $marketplacesCollection = $marketplaceModel->getCollection()
                                                       ->addFieldToFilter('status', Marketplace::STATUS_ENABLE)
                                                       ->addFieldToFilter(
                                                           'id',
                                                           ['in' => array_keys($dictionaryData)]
                                                       )
                                                       ->setOrder('sorder', 'ASC');

            $outdatedMarketplaces = [];
            foreach ($marketplacesCollection as $marketplace) {
                /** @var Marketplace $marketplace */
                $outdatedMarketplaces[$marketplace->getTitle()] = $dictionaryData[$marketplace->getId()];
            }

            $this->permanentCacheHelper->setValue(
                self::CACHE_KEY,
                $outdatedMarketplaces,
                ['walmart', 'marketplace'],
                60 * 60 * 24
            );
        }

        if (empty($outdatedMarketplaces)) {
            return [];
        }

        $tempTitle = $this->translationHelper->__(
            'M2E Pro requires action: Walmart marketplace data needs to be synchronized.
            Please update Walmart marketplaces.'
        );
        $textToTranslate = <<<TEXT
%marketplace_title% data was changed on Walmart. You need to resynchronize the marketplace(s) to correctly
associate your products with Walmart catalog.<br>
Please go to Walmart Integration > Configuration >
<a href="%url%" target="_blank">Marketplaces</a> and press <b>Update All Now</b>.
TEXT;

        $tempMessage = $this->translationHelper->__(
            $textToTranslate,
            implode(', ', array_keys($outdatedMarketplaces)),
            $this->urlBuilder->getUrl('m2epro/walmart_marketplace/index')
        );

        $editHash = sha1(self::CACHE_KEY . JsonHelper::encode($outdatedMarketplaces));
        $messageUrl = $this->urlBuilder->getUrl(
            'm2epro/walmart_marketplace/index',
            ['_query' => ['hash' => $editHash]]
        );

        return [
            $this->issueFactory->createNoticeDataObject($tempTitle, $tempMessage, $messageUrl),
        ];
    }

    /**
     * @return bool
     */
    public function isNeedProcess(): bool
    {
        return $this->walmartViewHelper->isInstallationWizardFinished() &&
            $this->walmartComponentHelper->isEnabled();
    }
}
