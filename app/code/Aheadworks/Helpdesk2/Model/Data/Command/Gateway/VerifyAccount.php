<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Data\Command\Gateway;

use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;
use Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway\BeforeVerify;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\Gateway\GoogleVerification;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\ConnectionInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Session\SessionManagerInterface;

/**
 * Verify Account
 */
class VerifyAccount implements CommandInterface
{
    /**#@+
     * Google related data
     */
    const IS_VERIFIED = 'aw_helpdesk2_is_account_verified';
    const ACCOUNT_VERIFY_ERROR = 'aw_helpdesk2_account_verify_error';
    /**#@-*/

    /**
     * @var SessionManagerInterface
     */
    private SessionManagerInterface $sessionManager;

    /**
     * @var GatewayRepositoryInterface
     */
    private GatewayRepositoryInterface $gatewayRepository;

    /**
     * @var ConnectionInterface
     */
    private ConnectionInterface $authModel;

    /**
     * @var DataObjectHelper
     */
    private DataObjectHelper $dataObjectHelper;

    /**
     * @param SessionManagerInterface $sessionManager
     * @param GatewayRepositoryInterface $gatewayRepository
     * @param ConnectionInterface $authModel
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        SessionManagerInterface $sessionManager,
        GatewayRepositoryInterface $gatewayRepository,
        ConnectionInterface $authModel,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->sessionManager = $sessionManager;
        $this->gatewayRepository = $gatewayRepository;
        $this->authModel = $authModel;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Execute command
     *
     * @param array $data
     * @return null|object
     * @throws LocalizedException
     */
    public function execute($data): ?object
    {
        $gatewayId = $this->sessionManager->getData(GoogleVerification::GATEWAY_ID_TO_VERIFY);
        if ((!isset($data['code'])) || !$gatewayId) {
            throw new \InvalidArgumentException('Account cannot be verified');
        }

        try {
            $gateway = $this->gatewayRepository->get($gatewayId);
            $this->mergeGatewayWithSessionData($gateway);
            $gateway = $this->authModel->actualizeAuthToken($gateway, $data['code']);
            $this->sessionManager->setData(
                self::IS_VERIFIED,
                [
                    GatewayDataInterface::IS_VERIFIED => (bool)$gateway->getIsVerified()
                ]
            );
        } catch (\Exception $e) {
            $gateway = null;
            $this->sessionManager->setData(self::ACCOUNT_VERIFY_ERROR, $e->getMessage());
        }

        return $gateway;
    }

    /**
     * Merge gateway data with stored in session
     *
     * @param GatewayDataInterface $gateway
     * @return void
     */
    private function mergeGatewayWithSessionData(GatewayDataInterface $gateway): void
    {
        $gatewayStoredParams = $this->sessionManager->getData(BeforeVerify::GATEWAY_DATA);
        if (is_array($gatewayStoredParams)) {
            unset($gatewayStoredParams[GatewayDataInterface::ACCESS_TOKEN]);
            $clientSecret = $gateway->getClientSecret();
            $this->dataObjectHelper->populateWithArray(
                $gateway,
                $gatewayStoredParams,
                GatewayDataInterface::class
            );
            $gateway->setClientSecret($clientSecret);
        }
    }
}
