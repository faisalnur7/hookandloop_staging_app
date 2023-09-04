<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\StorefrontLabel\Relation\Entity;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelEntityInterface;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterface;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterfaceFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\StorefrontLabel\Repository;
use Aheadworks\Helpdesk2\Model\StorefrontLabel\Resolver;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\StorefrontLabel\Relation\Entity
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var StorefrontLabelInterfaceFactory
     */
    private $storefrontLabelFactory;

    /**
     * @var Resolver
     */
    private $storefrontLabelResolver;

    /**
     * @param Repository $repository
     * @param DataObjectHelper $dataObjectHelper
     * @param StorefrontLabelInterfaceFactory $storefrontLabelFactory
     * @param Resolver $storefrontLabelResolver
     */
    public function __construct(
        Repository $repository,
        DataObjectHelper $dataObjectHelper,
        StorefrontLabelInterfaceFactory $storefrontLabelFactory,
        Resolver $storefrontLabelResolver
    ) {
        $this->repository = $repository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storefrontLabelFactory = $storefrontLabelFactory;
        $this->storefrontLabelResolver = $storefrontLabelResolver;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        /** @var StorefrontLabelEntityInterface $entity */
        if (!$entity->getId()) {
            return $entity;
        }

        $labelsData = $this->repository->get($entity);
        $labelsRecordsArray = $this->getLabelObjects($labelsData);
        $currentLabelsRecord = $this->storefrontLabelResolver->getLabelsForStore(
            $labelsRecordsArray,
            $arguments['store_id']
        );
        $entity
            ->setStorefrontLabels($labelsRecordsArray)
            ->setCurrentStorefrontLabel($currentLabelsRecord);

        return $entity;
    }

    /**
     * Retrieve storefront label from data array
     *
     * @param array $labelsData
     * @return StorefrontLabelInterface[]
     */
    protected function getLabelObjects($labelsData)
    {
        $labelsRecordsArray = [];
        foreach ($labelsData as $labelsDataRow) {
            /** @var StorefrontLabelInterface $labelsRecord */
            $labelsRecord = $this->storefrontLabelFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $labelsRecord,
                $labelsDataRow,
                StorefrontLabelInterface::class
            );
            $labelsRecordsArray[] = $labelsRecord;
        }

        return $labelsRecordsArray;
    }
}
