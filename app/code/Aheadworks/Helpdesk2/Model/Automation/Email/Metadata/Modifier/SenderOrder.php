<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier;

use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Department\Search\GatewayResolver;

/**
 * Class SenderOrder
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier
 */
class SenderOrder implements ModifierInterface
{
    /**
     * @var SenderResolverInterface
     */
    private $senderResolver;

    /**
     * @param GatewayResolver $gatewayResolver
     * @param SenderResolverInterface $senderResolver
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        SenderResolverInterface $senderResolver
    ) {
        $this->senderResolver = $senderResolver;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $senderData = $this->senderResolver->resolve('support', $eventData->getOrder()->getStoreId());
        $emailMetadata
            ->setSenderName($senderData['name'] ?? '')
            ->setSenderEmail($senderData['email'] ?? '');
        return $emailMetadata;
    }
}
