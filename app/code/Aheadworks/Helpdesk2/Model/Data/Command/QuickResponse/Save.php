<?php
namespace Aheadworks\Helpdesk2\Model\Data\Command\QuickResponse;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Api\QuickResponseRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterfaceFactory;

/**
 * Class Save
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\QuickResponse
 */
class Save implements CommandInterface
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var QuickResponseRepositoryInterface
     */
    private $quickResponseRepository;

    /**
     * @var QuickResponseInterfaceFactory
     */
    private $quickResponseFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param QuickResponseRepositoryInterface $quickResponseRepository
     * @param QuickResponseInterfaceFactory $quickResponseFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        QuickResponseRepositoryInterface $quickResponseRepository,
        QuickResponseInterfaceFactory $quickResponseFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->quickResponseRepository = $quickResponseRepository;
        $this->quickResponseFactory = $quickResponseFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute($quickResponseData)
    {
        $quickResponseData['updated_at'] = (new \DateTime())->format('Y-m-d H:i:s');
        $quickResponse = $this->getQuickResponseObject($quickResponseData);
        $this->dataObjectHelper->populateWithArray(
            $quickResponse,
            $quickResponseData,
            QuickResponseInterface::class
        );

        return $this->quickResponseRepository->save($quickResponse);
    }

    /**
     * Get quick response object
     *
     * @param array $quickResponseData
     * @return QuickResponseInterface
     * @throws NoSuchEntityException
     */
    private function getQuickResponseObject($quickResponseData)
    {
        return isset($gatewayData[QuickResponseInterface::ID])
            ? $this->quickResponseRepository->get($quickResponseData[QuickResponseInterface::ID])
            : $this->quickResponseFactory->create();
    }
}
