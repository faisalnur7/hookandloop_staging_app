<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables;

use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Source\Email\Variables as EmailVariables;
use Aheadworks\Helpdesk2\Model\UrlBuilder;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ExternalUrl
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables
 */
class ExternalUrl implements ModifierInterface
{
    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * Store manager
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param UrlBuilder $urlBuilder
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        UrlBuilder $urlBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $templateVariables = $emailMetadata->getTemplateVariables();
        $externalLink = $eventData->getTicket()->getExternalLink();
        $storeId = $eventData->getTicket()->getStoreId();
        $store = $this->storeManager->getStore($storeId);
        $templateVariables[EmailVariables::EXTERNAL_URL] = $this->urlBuilder->getTicketExternalLink($externalLink, $store);
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
