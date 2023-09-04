<?php
namespace Aheadworks\Helpdesk2\Model\Data\Command\Gateway;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterfaceFactory;

/**
 * Class Save
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Gateway
 */
class Save implements CommandInterface
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var GatewayRepositoryInterface
     */
    private $gatewayRepository;

    /**
     * @var GatewayDataInterfaceFactory
     */
    private $gatewayFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param GatewayRepositoryInterface $gatewayRepository
     * @param GatewayDataInterfaceFactory $gatewayFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        GatewayRepositoryInterface $gatewayRepository,
        GatewayDataInterfaceFactory $gatewayFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->gatewayRepository = $gatewayRepository;
        $this->gatewayFactory = $gatewayFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute($gatewayData)
    {
        $gateway = $this->getGatewayObject($gatewayData);
        $this->dataObjectHelper->populateWithArray(
            $gateway,
            $gatewayData,
            GatewayDataInterface::class
        );

        return $this->gatewayRepository->save($gateway);
    }

    /**
     * Get gateway object
     *
     * @param array $gatewayData
     * @return GatewayDataInterface
     * @throws NoSuchEntityException
     */
    private function getGatewayObject($gatewayData)
    {
        return isset($gatewayData[GatewayDataInterface::ID])
            ? $this->gatewayRepository->get($gatewayData[GatewayDataInterface::ID])
            : $this->gatewayFactory->create();
    }
}
