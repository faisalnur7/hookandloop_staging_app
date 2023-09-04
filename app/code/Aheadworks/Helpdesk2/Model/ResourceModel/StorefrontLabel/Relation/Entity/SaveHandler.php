<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\StorefrontLabel\Relation\Entity;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelEntityInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\StorefrontLabel\Repository;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\StorefrontLabel\Relation\Entity
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        /** @var StorefrontLabelEntityInterface $entity */
        $this->repository->save($entity);
        return $entity;
    }
}
