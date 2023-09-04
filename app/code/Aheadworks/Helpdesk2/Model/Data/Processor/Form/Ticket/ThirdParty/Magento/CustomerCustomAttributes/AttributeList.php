<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket\ThirdParty\Magento\CustomerCustomAttributes;

use Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket\ThirdParty\CustomerAttribute\AbstractAttributeList;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\ThirdPartyModule\Aheadworks\CustomerAttributes\CustomerLoader;
use Aheadworks\Helpdesk2\Model\ThirdPartyModule\Magento\CustomerCustomAttributes\AttributeMetaProvider;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket\ThirdParty\CustomerAttribute\ComponentDataResolver;

/**
 * Class AttributeList
 */
class AttributeList extends AbstractAttributeList
{
    /**
     * AttributeList constructor.
     *
     * @param TicketRepositoryInterface $ticketRepository
     * @param CustomerLoader $customerLoader
     * @param ComponentDataResolver $componentDataResolver
     * @param AttributeMetaProvider $attributeMetaProvider
     */
    public function __construct(
        TicketRepositoryInterface $ticketRepository,
        CustomerLoader $customerLoader,
        ComponentDataResolver $componentDataResolver,
        private AttributeMetaProvider $attributeMetaProvider
    ) {
        parent::__construct($ticketRepository, $customerLoader, $componentDataResolver);
    }

    /**
     * Prepare meta data for form
     *
     * @param array $meta
     * @return array
     * @throws LocalizedException
     */
    public function prepareMetaData($meta): array
    {
        $meta['general']['children'] = $this->attributeMetaProvider->getHelpDeskAttributesMeta();
        return $meta;
    }
}
