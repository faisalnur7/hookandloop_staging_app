<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket\MassAction\ChangeStatus;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Magento\Framework\UrlInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as SourceStatus;

class Options implements \JsonSerializable
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var SourceStatus
     */
    private $sourceStatus;

    /**
     * @param UrlInterface $urlBuilder
     * @param SourceStatus $sourceStatus
     */
    public function __construct(
        UrlInterface $urlBuilder,
        SourceStatus $sourceStatus
    ) {
        $this->sourceStatus = $sourceStatus;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get ticket statuses
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function jsonSerialize(): array
    {
        $returnArray = [];
        foreach ($this->sourceStatus->toOptionArray() as $item) {
            $item['url'] = $this->urlBuilder->getUrl(
                'aw_helpdesk2/ticket/massChangeStatus',
                [TicketInterface::STATUS_ID => $item['value']]
            );
            $item['type'] = 'type_id_' . $item['value'];
            $item['confirm']['message'] = __('Are you sure you want to change status selected tickets?');
            $returnArray[] = $item;
        }
        return $returnArray;
    }
}
