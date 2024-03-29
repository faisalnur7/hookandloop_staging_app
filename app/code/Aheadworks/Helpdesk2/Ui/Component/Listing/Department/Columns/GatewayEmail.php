<?php
namespace Aheadworks\Helpdesk2\Ui\Component\Listing\Department\Columns;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;

/**
 * Class GatewayEmail
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Listing\Department\Columns
 */
class GatewayEmail extends Column
{
    /**
     * @var GatewayRepositoryInterface
     */
    private $gatewayRepository;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param GatewayRepositoryInterface $gatewayRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        GatewayRepositoryInterface $gatewayRepository,
        array $components = [],
        array $data = []

    ) {
        $this->gatewayRepository = $gatewayRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritdoc
     *
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as &$item) {
            $gatewayIds = $item[DepartmentInterface::GATEWAY_IDS];
            $item[$this->getData('name')] = !empty($gatewayIds)
                ? $this->getGatewayName($gatewayIds)
                : __('Not Assigned');
        }

        return $dataSource;
    }

    /**
     * Get gateway name
     *
     * @param array $gatewayIds
     * @return string
     * @throws NoSuchEntityException
     */
    private function getGatewayName($gatewayIds)
    {
        $emailGateway = reset($gatewayIds);
        $gateway = $this->gatewayRepository->get($emailGateway);
        return $gateway->getName();
    }
}
