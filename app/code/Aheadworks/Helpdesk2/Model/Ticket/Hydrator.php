<?php
namespace Aheadworks\Helpdesk2\Model\Ticket;

use Magento\Framework\EntityManager\HydratorInterface;

/**
 * Class Hydrator
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket
 */
class Hydrator implements HydratorInterface
{
    /**
     * @inheritdoc
     */
    public function extract($entity)
    {
        // todo check object processor can be applied
        return $entity->getData();
    }

    /**
     * @inheritdoc
     */
    public function hydrate($entity, array $data)
    {
        $entity->setData(array_merge($entity->getData(), $data));
        return $entity;
    }
}
