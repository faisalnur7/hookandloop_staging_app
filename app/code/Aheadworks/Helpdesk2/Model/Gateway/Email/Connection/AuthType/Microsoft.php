<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType;

use Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\Microsoft\AdapterFactory as MicrosoftAdapterFactory;
use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;

/**
 * Microsoft auth type connection
 */
class Microsoft extends OAuthModel
{
    /**
     * @var MicrosoftAdapterFactory
     */
    protected MicrosoftAdapterFactory $adapterFactory;

    /**
     * @param OAuthConnector $gatewayConnector
     * @param MicrosoftAdapterFactory $adapterFactory
     * @param GatewayRepositoryInterface $gatewayRepository
     */
    public function __construct(
        OAuthConnector $gatewayConnector,
        GatewayRepositoryInterface $gatewayRepository,
        MicrosoftAdapterFactory $adapterFactory
    ) {
        parent::__construct($gatewayConnector, $gatewayRepository);
        $this->adapterFactory = $adapterFactory;
    }

    /**
     * Get adapter
     *
     * @return AdapterInterface
     */
    protected function getAdapter(): AdapterInterface
    {
        return $this->adapterFactory->create();
    }
}
