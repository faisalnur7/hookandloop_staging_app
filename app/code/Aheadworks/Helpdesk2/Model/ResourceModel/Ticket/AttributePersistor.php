<?php
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket;

use Magento\Framework\Model\Entity\ScopeInterface;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\ResourceModel\AttributePersistor as EavAttributePersistor;
use Aheadworks\Helpdesk2\Model\Ticket\Attribute as TicketEavAttribute;

/**
 * Class AttributePersistor
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket
 */
class AttributePersistor extends EavAttributePersistor
{
    /**
     * @inheritdoc
     */
    protected function getScopeValue(ScopeInterface $scope, AbstractAttribute $attribute, $useDefault = false)
    {
        if ($attribute instanceof TicketEavAttribute) {
            $useDefault = $useDefault || $attribute->isScopeGlobal();
        }

        return parent::getScopeValue($scope, $attribute, $useDefault);
    }
}
