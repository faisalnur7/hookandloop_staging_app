<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier;

use Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\SenderResolverInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Department\Search\GatewayResolver;

/**
 * Class Sender
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier
 */
class Sender implements ModifierInterface
{
    /**
     * @var GatewayResolver
     */
    private $gatewayResolver;

    /**
     * @var SenderResolverInterface
     */
    private $senderResolver;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param GatewayResolver $gatewayResolver
     * @param SenderResolverInterface $senderResolver
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        GatewayResolver $gatewayResolver,
        SenderResolverInterface $senderResolver,
        StoreManagerInterface $storeManager
    ) {
        $this->gatewayResolver = $gatewayResolver;
        $this->senderResolver = $senderResolver;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $gateway = $this->gatewayResolver->resolveGatewayForDepartmentId(
            $eventData->getTicket()->getDepartmentId()
        );
        if ($gateway && $gateway->getEmail()) {
            /** @var Store $store */
            $store = $this->storeManager->getStore($eventData->getTicket()->getStoreId());
            $emailMetadata
                ->setSenderName($store->getFrontendName())
                ->setSenderEmail($gateway->getEmail());
        } else {
           list($email, $name) = $this->senderResolver->resolve($eventData);
            $emailMetadata
                ->setSenderName($name)
                ->setSenderEmail($email);
        }

        return $emailMetadata;
    }
}
