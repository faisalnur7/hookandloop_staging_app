<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Helper\Component\Ebay\Template\Switcher;

class DataLoader
{
    /** @var \Ess\M2ePro\Model\ActiveRecord\Component\Parent\Ebay\Factory */
    private $ebayFactory;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;
    /** @var \Ess\M2ePro\Model\Ebay\Template\Manager */
    private $templateManager;
    /** @var \Ess\M2ePro\Model\Ebay\Template\ManagerFactory */
    private $templateManagerFactory;
    /** @var \Ess\M2ePro\Helper\Magento\Attribute */
    private $magentoAttributeHelper;
    /** @var \Ess\M2ePro\Helper\Magento\AttributeSet */
    private $magentoAttributeSetHelper;
    /** @var \Ess\M2ePro\Helper\Data\GlobalData */
    private $globalData;

    /**
     * @param \Ess\M2ePro\Helper\Data\GlobalData $globalData
     * @param \Ess\M2ePro\Model\ActiveRecord\Component\Parent\Ebay\Factory $ebayFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Ess\M2ePro\Model\Ebay\Template\Manager $templateManager
     * @param \Ess\M2ePro\Model\Ebay\Template\ManagerFactory $templateManagerFactory
     * @param \Ess\M2ePro\Helper\Magento\Attribute $magentoAttributeHelper
     * @param \Ess\M2ePro\Helper\Magento\AttributeSet $magentoAttributeSetHelper
     */
    public function __construct(
        \Ess\M2ePro\Helper\Data\GlobalData $globalData,
        \Ess\M2ePro\Model\ActiveRecord\Component\Parent\Ebay\Factory $ebayFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ess\M2ePro\Model\Ebay\Template\Manager $templateManager,
        \Ess\M2ePro\Model\Ebay\Template\ManagerFactory $templateManagerFactory,
        \Ess\M2ePro\Helper\Magento\Attribute $magentoAttributeHelper,
        \Ess\M2ePro\Helper\Magento\AttributeSet $magentoAttributeSetHelper
    ) {
        $this->ebayFactory = $ebayFactory;
        $this->storeManager = $storeManager;
        $this->templateManager = $templateManager;
        $this->templateManagerFactory = $templateManagerFactory;
        $this->magentoAttributeHelper = $magentoAttributeHelper;
        $this->magentoAttributeSetHelper = $magentoAttributeSetHelper;
        $this->globalData = $globalData;
    }

    public function load($source, array $params = [])
    {
        $data = null;

        if ($source instanceof \Ess\M2ePro\Helper\Data\Session) {
            $data = $this->getDataFromSession($source, $params);
        }
        if ($source instanceof \Ess\M2ePro\Model\Listing) {
            $data = $this->getDataFromListing($source, $params);
        }
        if ($source instanceof \Ess\M2ePro\Model\ResourceModel\Listing\Product\Collection) {
            $data = $this->getDataFromListingProducts($source, $params);
        }
        if ($this->isTemplateInstance($source)) {
            $data = $this->getDataFromTemplate($source, $params);
        }
        if ($source instanceof \Magento\Framework\App\RequestInterface) {
            $data = $this->getDataFromRequest($source, $params);
        }

        if ($data === null) {
            throw new \InvalidArgumentException('Data source is invalid.');
        }

        $account = null;
        if ($data['account_id']) {
            $account = $this->ebayFactory->getCachedObjectLoaded('Account', $data['account_id']);
        }

        $marketplace = null;
        if ($data['marketplace_id']) {
            $marketplace = $this->ebayFactory->getCachedObjectLoaded('Marketplace', $data['marketplace_id']);
        }

        $storeId = (int)$data['store_id'];

        $attributeSets = $data['attribute_sets'];
        $attributes = $this->magentoAttributeHelper->getAll();

        $displayUseDefaultOption = $data['display_use_default_option'];

        $global = $this->globalData;

        $global->setValue('ebay_account', $account);
        $global->setValue('ebay_marketplace', $marketplace);
        $global->setValue('ebay_store', $this->storeManager->getStore($storeId));
        $global->setValue('ebay_attribute_sets', $attributeSets);
        $global->setValue('ebay_attributes', $attributes);
        $global->setValue('ebay_display_use_default_option', $displayUseDefaultOption);

        foreach ($data['templates'] as $nick => $templateData) {
            $template = $this->templateManager->setTemplate($nick)->getTemplateModel();

            if ($templateData['id']) {
                $template->load($templateData['id']);
            }

            $global->setValue("ebay_template_{$nick}", $template);
            $global->setValue("ebay_template_mode_{$nick}", $templateData['mode']);
            $global->setValue("ebay_template_force_parent_{$nick}", $templateData['force_parent']);
        }
    }

    //########################################

    private function getDataFromSession(\Ess\M2ePro\Helper\Data\Session $source, array $params = [])
    {
        if (!isset($params['session_key'])) {
            throw new \Ess\M2ePro\Model\Exception\Logic('Session key is not defined.');
        }
        $sessionKey = $params['session_key'];
        $sessionData = $source->getValue($sessionKey);

        $accountId = isset($sessionData['account_id']) ? $sessionData['account_id'] : null;
        $marketplaceId = isset($sessionData['marketplace_id']) ? $sessionData['marketplace_id'] : null;
        $storeId = isset($sessionData['store_id']) ? $sessionData['store_id'] : null;
        $attributeSets = $this->magentoAttributeSetHelper
            ->getAll(\Ess\M2ePro\Helper\Magento\AbstractHelper::RETURN_TYPE_IDS);

        $templates = [];

        foreach ($this->templateManager->getAllTemplates() as $nick) {
            $templateId = isset($sessionData["template_id_{$nick}"]) ? $sessionData["template_id_{$nick}"] : null;
            $templateMode = isset($sessionData["template_id_{$nick}"]) ? $sessionData["template_mode_{$nick}"] : null;

            if (empty($templateMode)) {
                $templateMode = \Ess\M2ePro\Model\Ebay\Template\Manager::MODE_CUSTOM;
            }

            $templates[$nick] = [
                'id'           => $templateId,
                'mode'         => $templateMode,
                'force_parent' => false,
            ];
        }

        return [
            'account_id'                 => $accountId,
            'marketplace_id'             => $marketplaceId,
            'store_id'                   => $storeId,
            'attribute_sets'             => $attributeSets,
            'display_use_default_option' => false,
            'templates'                  => $templates,
        ];
    }

    private function getDataFromListing(\Ess\M2ePro\Model\Listing $source, array $params = [])
    {
        $accountId = $source->getAccountId();
        $marketplaceId = $source->getMarketplaceId();
        $storeId = $source->getStoreId();
        $attributeSets = $this->magentoAttributeSetHelper
            ->getAll(\Ess\M2ePro\Helper\Magento\AbstractHelper::RETURN_TYPE_IDS);

        $templates = [];

        foreach ($this->templateManager->getAllTemplates() as $nick) {
            $manager = $this->templateManagerFactory->create()
                                                    ->setTemplate($nick)
                                                    ->setOwnerObject($source->getChildObject());

            $templateId = $manager->getIdColumnValue();
            $templateMode = $manager->getModeValue();

            $templates[$nick] = [
                'id'           => $templateId,
                'mode'         => $templateMode,
                'force_parent' => false,
            ];
        }

        return [
            'account_id'                 => $accountId,
            'marketplace_id'             => $marketplaceId,
            'store_id'                   => $storeId,
            'attribute_sets'             => $attributeSets,
            'display_use_default_option' => false,
            'templates'                  => $templates,
        ];
    }

    private function getDataFromListingProducts($source, array $params = [])
    {
        /** @var \Ess\M2ePro\Model\Listing\Product $listingProductFirst */
        $listingProductFirst = $source->getFirstItem();

        $productIds = [];
        foreach ($source as $listingProduct) {
            $productIds[] = $listingProduct->getData('product_id');
        }

        $accountId = $listingProductFirst->getListing()->getAccountId();
        $marketplaceId = $listingProductFirst->getListing()->getMarketplaceId();
        $storeId = $listingProductFirst->getListing()->getStoreId();
        $attributeSets = $this->magentoAttributeSetHelper
            ->getFromProducts($productIds, \Ess\M2ePro\Helper\Magento\AbstractHelper::RETURN_TYPE_IDS);

        $templates = [];

        foreach ($this->templateManager->getAllTemplates() as $nick) {
            $templateId = null;
            $templateMode = null;
            $forceParent = false;

            if ($source->getSize() <= 200) {
                foreach ($source->getItems() as $listingProduct) {
                    $manager = $this->templateManagerFactory->create()
                                                            ->setTemplate($nick)
                                                            ->setOwnerObject($listingProduct->getChildObject());

                    $currentProductTemplateId = $manager->getIdColumnValue();
                    $currentProductTemplateMode = $manager->getModeValue();

                    if ($templateId === null && $templateMode === null) {
                        $templateId = $currentProductTemplateId;
                        $templateMode = $currentProductTemplateMode;
                        continue;
                    }

                    if ($templateId != $currentProductTemplateId || $templateMode != $currentProductTemplateMode) {
                        $templateId = null;
                        $templateMode = \Ess\M2ePro\Model\Ebay\Template\Manager::MODE_PARENT;
                        $forceParent = true;
                        break;
                    }
                }
            } else {
                $forceParent = true;
            }

            if ($templateMode === null) {
                $templateMode = \Ess\M2ePro\Model\Ebay\Template\Manager::MODE_PARENT;
            }

            $templates[$nick] = [
                'id'           => $templateId,
                'mode'         => $templateMode,
                'force_parent' => $forceParent,
            ];
        }

        return [
            'account_id'                 => $accountId,
            'marketplace_id'             => $marketplaceId,
            'store_id'                   => $storeId,
            'attribute_sets'             => $attributeSets,
            'display_use_default_option' => true,
            'templates'                  => $templates,
        ];
    }

    private function getDataFromTemplate($source, array $params = [])
    {
        $attributeSets = $this->magentoAttributeSetHelper
            ->getAll(\Ess\M2ePro\Helper\Magento\AbstractHelper::RETURN_TYPE_IDS);

        $marketplaceId = null;
        if (isset($params['marketplace_id'])) {
            $marketplaceId = (int)$params['marketplace_id'];
        }

        $nick = $this->getTemplateNick($source);

        return [
            'account_id'                 => null,
            'marketplace_id'             => $marketplaceId,
            'store_id'                   => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            'attribute_sets'             => $attributeSets,
            'display_use_default_option' => true,
            'templates'                  => [
                $nick => [
                    'id'           => $source->getId(),
                    'mode'         => \Ess\M2ePro\Model\Ebay\Template\Manager::MODE_TEMPLATE,
                    'force_parent' => false,
                ],
            ],
        ];
    }

    private function getDataFromRequest(\Magento\Framework\App\RequestInterface $source, array $params = [])
    {
        $id = $source->getParam('id');
        $nick = $source->getParam('nick');
        $mode = $source->getParam('mode', \Ess\M2ePro\Model\Ebay\Template\Manager::MODE_CUSTOM);

        $attributeSets = $source->getParam('attribute_sets', '');
        $attributeSets = array_filter(explode(',', $attributeSets));

        if (empty($attributeSets)) {
            $attributeSets = $this->magentoAttributeSetHelper
                ->getAll(\Ess\M2ePro\Helper\Magento\AbstractHelper::RETURN_TYPE_IDS);
        }

        return [
            'account_id'                 => $source->getParam('account_id'),
            'marketplace_id'             => $source->getParam('marketplace_id'),
            'store_id'                   => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            'attribute_sets'             => $attributeSets,
            'display_use_default_option' => (bool)$source->getParam('display_use_default_option'),
            'templates'                  => [
                $nick => [
                    'id'           => $id,
                    'mode'         => $mode,
                    'force_parent' => false,
                ],
            ],
        ];
    }

    // ----------------------------------------

    private function getTemplateNick($source)
    {
        if (!$this->isHorizontalTemplate($source)) {
            return $source->getNick();
        }

        $nick = null;

        if ($source instanceof \Ess\M2ePro\Model\Template\SellingFormat) {
            $nick = \Ess\M2ePro\Model\Ebay\Template\Manager::TEMPLATE_SELLING_FORMAT;
        } elseif ($source instanceof \Ess\M2ePro\Model\Template\Synchronization) {
            $nick = \Ess\M2ePro\Model\Ebay\Template\Manager::TEMPLATE_SYNCHRONIZATION;
        } elseif ($source instanceof \Ess\M2ePro\Model\Template\Description) {
            $nick = \Ess\M2ePro\Model\Ebay\Template\Manager::TEMPLATE_DESCRIPTION;
        }

        return $nick;
    }

    /**
     * @param $source
     *
     * @return bool
     */
    private function isTemplateInstance($source): bool
    {
        if (
            $source instanceof \Ess\M2ePro\Model\Ebay\Template\Shipping
            || $source instanceof \Ess\M2ePro\Model\Ebay\Template\ReturnPolicy
            || $source instanceof \Ess\M2ePro\Model\Template\SellingFormat
            || $source instanceof \Ess\M2ePro\Model\Template\Description
            || $source instanceof \Ess\M2ePro\Model\Template\Synchronization
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param $source
     *
     * @return bool
     */
    private function isHorizontalTemplate($source): bool
    {
        if (
            $source instanceof \Ess\M2ePro\Model\Template\SellingFormat ||
            $source instanceof \Ess\M2ePro\Model\Template\Synchronization ||
            $source instanceof \Ess\M2ePro\Model\Template\Description
        ) {
            return true;
        }

        return false;
    }
}
