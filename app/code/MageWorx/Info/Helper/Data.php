<?php
/**
 * Copyright ©  MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Info\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Store\Model\ScopeInterface;


class Data extends AbstractHelper
{
    const USE_MARKETPLACE_URL = false;

    /**
     * @var string
     */
    const MAGEWORX_SITE = 'https://www.mageworx.com';

    /**
     * XML config path for updates notification
     */
    const XML_PATH_UPDATES_NOTIFICATION = 'mageworx_settings/general/updates_notification';

    /**
     * XML config path for offers notification
     */
    const XML_PATH_OFFERS_NOTIFICATION = 'mageworx_settings/general/offers_notification';

    /**
     * XML config path for offers notification
     */
    const XML_PATH_EXTENSION_INFO_AUTOLOAD = 'mageworx_settings/extensions/extension_info_autoload';

    /**
     * XML config path for offers notification
     */
    const XML_PATH_INSTALLED_EXTENSION_DATA = 'mageworx_settings/general/installed_data';

    /**
     * XML config path for offers notification
     */
    const XML_PATH_RECOMMENDED_EXTENSION_DATA = 'mageworx_settings/general/recommended_data';

    /**
     * @var string
     */
    const CACHE_IDENTIFIER_EXTENSION_LIST = 'mageworx_extension_list_lastcheck';

    /**
     * @var int
     */
    const EXTENSION_LIST_FREQUENCY = 60 * 60 * 24;

    /**
     * @var string
     */
    const EXTENSION_LIST_URL = self::MAGEWORX_SITE . '/extensions_list.js';

    /**
     * @var string
     */
    const EXTENSION_REVIEW_URL = self::MAGEWORX_SITE . '/infoprovider/index/review';

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * Cache Manager
     *
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cacheManager;

    /**
     * @var \Magento\Framework\HTTP\Adapter\CurlFactory
     *
     */
    protected $curlFactory;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \MageWorx\Info\Model\MetaPackageList
     */
    protected $metaPackageList;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config\Data\CollectionFacory
     */
    protected $configCollectionFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * Data constructor.
     *
     * @param \MageWorx\Info\Model\MetaPackageList $metaPackageList
     * @param Context $context
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\CacheInterface $cacheManager
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configCollectionFactory
     */
    public function __construct(
        ModuleManager $moduleManager,
        \MageWorx\Info\Model\MetaPackageList $metaPackageList,
        Context $context,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\CacheInterface $cacheManager,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configCollectionFactory
    ) {
        parent::__construct($context);
        $this->moduleManager           = $moduleManager;
        $this->metaPackageList         = $metaPackageList;
        $this->configWriter            = $configWriter;
        $this->cacheManager            = $cacheManager;
        $this->curlFactory             = $curlFactory;
        $this->productMetadata         = $productMetadata;
        $this->configCollectionFactory = $configCollectionFactory;
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isUpdatesNotificationEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_UPDATES_NOTIFICATION
        );
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isOffersNotificationEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_OFFERS_NOTIFICATION
        );
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isExtensionInfoAutoloadEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_EXTENSION_INFO_AUTOLOAD
        );
    }

    public function isNotificationExtensionEnabled()
    {
        return $this->moduleManager->isEnabled('Magento_AdminNotification');
    }

    /**
     * @return array
     */
    public function getRecommendedExtensionsData()
    {
        /** @var \Magento\Config\Model\ResourceModel\Config\Data\Collection $configCollection */
        $configCollection = $this->configCollectionFactory->create();
        $configCollection
            ->addFieldToFilter('scope', \Magento\Config\Block\System\Config\Form::SCOPE_DEFAULT)
            ->addFieldToFilter('scope_id', 0)
            ->addFieldToFilter('path', self::XML_PATH_RECOMMENDED_EXTENSION_DATA);

        $result = $configCollection->count() ? $configCollection->getFirstItem()->getValue() : '';

        return json_decode($result, true);
    }


    /**
     * @return array
     */
    public function getInstalledExtensionsData()
    {
        /** @var \Magento\Config\Model\ResourceModel\Config\Data\Collection $configCollection */
        $configCollection = $this->configCollectionFactory->create();
        $configCollection
            ->addFieldToFilter('scope', \Magento\Config\Block\System\Config\Form::SCOPE_DEFAULT)
            ->addFieldToFilter('scope_id', 0)
            ->addFieldToFilter('path', self::XML_PATH_INSTALLED_EXTENSION_DATA);

        $result = $configCollection->count() ? $configCollection->getFirstItem()->getValue() : '';

        return json_decode($result, true);
    }

    /**
     * @param array $data
     */
    public function setRecommendedExtensionsData($data)
    {
        if (!empty($data)) {
            $this->configWriter->save(
                self::XML_PATH_RECOMMENDED_EXTENSION_DATA,
                json_encode($data)
            );
        }
    }

    /**
     * @param array $data
     */
    public function setInstalledExtensionsData($data)
    {
        if (!empty($data)) {
            $this->configWriter->save(
                self::XML_PATH_INSTALLED_EXTENSION_DATA,
                json_encode($data)
            );
        }
    }

    /**
     * @param bool $forceUpdate
     * @return $this
     */
    public function checkExtensionListUpdate($forceUpdate = false)
    {
        if (!$forceUpdate) {
            if (self::EXTENSION_LIST_FREQUENCY + $this->getLastExtensionListUpdate() > time()) {
                return $this;
            }
        }

        $extensionData = json_decode($this->loadExtensionsData(), true);

        if (empty($extensionData)) {
            $this->setLastExtensionListUpdate();

            return $this;
        }

        $installedData = [];
        foreach ($this->metaPackageList->getInstalledExtensionCodes() as $ext) {
            if (isset($extensionData['extensions'][$ext])) {
                $installedData[$ext] = $extensionData['extensions'][$ext];
            }
        }

        $this->setInstalledExtensionsData($installedData);

        if (isset($extensionData['recommended'])) {
            $recommendedData = [];

            foreach ($extensionData['recommended'] as $ext) {
                if (isset($extensionData['extensions'][$ext])) {
                    $recommendedData[$ext] = $extensionData['extensions'][$ext];
                }
            }

            $this->setRecommendedExtensionsData($recommendedData);
        }

        $this->setLastExtensionListUpdate();

        return $this;
    }

    /**
     * Retrieve extension list last update time
     *
     * @return int
     */
    protected function getLastExtensionListUpdate()
    {
        return $this->cacheManager->load(self::CACHE_IDENTIFIER_EXTENSION_LIST);
    }

    /**
     * Set feed last update time (now)
     *
     * @return $this
     */
    protected function setLastExtensionListUpdate()
    {
        $this->cacheManager->save(time(), self::CACHE_IDENTIFIER_EXTENSION_LIST);

        return $this;
    }

    /**
     * @return array[]|bool|false|string|string[]
     */
    protected function loadExtensionsData()
    {
        $curl = $this->curlFactory->create();
        $curl->setConfig(
            [
                'useragent' => $this->productMetadata->getName()
                    . '/' . $this->productMetadata->getVersion()
                    . '/' . $this->productMetadata->getEdition(),
                'referer'   => $this->_urlBuilder->getUrl('*/*/*'),
                'timeout'   => 2,
            ]
        );

        $curl->write('GET', self::EXTENSION_LIST_URL . '?date=' . date('Y-m-d'), '1.0');
        $data = $curl->read();
        if ($data === false) {
            return false;
        }
        $data = preg_split('/^\r?$/m', $data, 2);
        $data = trim($data[1]);
        $curl->close();

        return $data;
    }

    /**
     * @return string
     */
    public function getStoreUrl()
    {
        return $this->scopeConfig->getValue(
            'web/unsecure/base_url',
            ScopeInterface::SCOPE_STORE,
            0
        );
    }

    /**
     * @return string
     */
    public function getReviewUrl()
    {
        return self::EXTENSION_REVIEW_URL;
    }

    /**
     * @param array $data
     * @return array[]|bool|false|string|string[]
     */
    public function sendReviewData($data)
    {
        $curl = $this->curlFactory->create();
        $curl->setConfig(
            [
                'useragent' => $this->productMetadata->getName()
                    . '/' . $this->productMetadata->getVersion()
                    . '/' . $this->productMetadata->getEdition(),
                'referer'   => $this->_urlBuilder->getUrl('*/*/*'),
                'timeout'   => 2,
            ]
        );
        $curl->write('POST', $this->getReviewUrl(), '1.1', [], $data);
        $result = $curl->read();
        if ($result === false) {
            return false;
        }
        $result = preg_split('/^\r?$/m', $result, 2);
        $result = trim($result[1]);
        $curl->close();

        return $result;
    }

}
