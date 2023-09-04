<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType;

use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\Google\AdapterFactory as GoogleAdapterFactory;

/**
 * Google auth type connection
 */
class Google extends OAuthModel
{
    /**
     * @var GoogleAdapterFactory
     */
    protected GoogleAdapterFactory $adapterFactory;

    /**
     * @param OAuthConnector $gatewayConnector
     * @param GoogleAdapterFactory $adapterFactory
     * @param GatewayRepositoryInterface $gatewayRepository
     */
    public function __construct(
        OAuthConnector $gatewayConnector,
        GatewayRepositoryInterface $gatewayRepository,
        GoogleAdapterFactory $adapterFactory
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
