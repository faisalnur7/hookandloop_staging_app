<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Setup;

use Magento\Eav\Setup\EavSetup;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Ticket as TicketModel;
use Aheadworks\Helpdesk2\Model\Ticket\Attribute as AttributeModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResourceModel;

class TicketEavSetup extends EavSetup
{
    /**
     * Gets default entities and attributes
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        $entity = TicketModel::ENTITY;
        $entities = [
            $entity => [
                'entity_model' => TicketResourceModel::class,
                'table' => TicketResourceModel::TICKET_ENTITY_TABLE_NAME,
                'id_field' => TicketInterface::ENTITY_ID,
                'attribute_model' => AttributeModel::class,
                'additional_attribute_table' => TicketResourceModel::TICKET_EAV_ATTRIBUTE_TABLE_NAME,
                'attributes' => [
                    TicketInterface::UID => [
                        'type' => 'static',
                    ],
                    TicketInterface::RATING => [
                        'type' => 'static',
                    ],
                    TicketInterface::CUSTOMER_RATING => [
                        'type' => 'static',
                    ],
                    TicketInterface::SUBJECT => [
                        'type' => 'static',
                    ],
                    TicketInterface::DEPARTMENT_ID => [
                        'type' => 'static',
                    ],
                    TicketInterface::AGENT_ID => [
                        'type' => 'static',
                    ],
                    TicketInterface::IS_LOCKED => [
                        'type' => 'static',
                    ],
                    TicketInterface::STORE_ID => [
                        'type' => 'static',
                    ],
                    TicketInterface::CUSTOMER_ID => [
                        'type' => 'static',
                    ],
                    TicketInterface::CUSTOMER_EMAIL => [
                        'type' => 'static',
                    ],
                    TicketInterface::CUSTOMER_NAME => [
                        'type' => 'static',
                    ],
                    TicketInterface::CC_RECIPIENTS => [
                        'type' => 'static',
                    ],
                    TicketInterface::STATUS_ID => [
                        'type' => 'static',
                    ],
                    TicketInterface::PRIORITY_ID => [
                        'type' => 'static',
                    ],
                    TicketInterface::INTERNAL_NOTE => [
                        'type' => 'static',
                    ],
                    TicketInterface::CREATED_AT => [
                        'type' => 'static',
                    ],
                    TicketInterface::UPDATED_AT => [
                        'type' => 'static',
                    ],
                    TicketInterface::EXTERNAL_LINK => [
                        'type' => 'static',
                    ],
                    TicketInterface::TELEPHONE => [
                        'type' => 'static',
                    ]
                ],
            ],
        ];

        return $entities;
    }
}
