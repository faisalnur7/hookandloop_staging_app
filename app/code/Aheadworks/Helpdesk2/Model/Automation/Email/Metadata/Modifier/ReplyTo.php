<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier;

use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Department\Search\GatewayResolver;

/**
 * Class Sender
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier
 */
class ReplyTo implements ModifierInterface
{
    /**
     * @var GatewayResolver
     */
    private $gatewayResolver;

    /**
     * @param GatewayResolver $gatewayResolver
     */
    public function __construct(
        GatewayResolver $gatewayResolver
    ) {
        $this->gatewayResolver = $gatewayResolver;
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
            $emailMetadata->setEmailReplyTo($gateway->getEmail());
        }

        return $emailMetadata;
    }
}
