<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Data\Command\Ticket\Merge;

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Merge\MergeHandler;
use Aheadworks\Helpdesk2\Model\Ticket\Merge\MergeValidator;
use Aheadworks\Helpdesk2\Model\Ticket\Merge\RequestDataProvider;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Merge
 */
class Merge implements CommandInterface
{
    /**
     * Merge constructor.
     *
     * @param MergeHandler $mergeHandler
     * @param MergeValidator $mergeValidator
     * @param RequestDataProvider $requestDataProvider
     */
    public function __construct(
        private MergeHandler $mergeHandler,
        private MergeValidator $mergeValidator,
        private RequestDataProvider $requestDataProvider,
    ) {
    }

    /**
     * Execute command
     *
     * @param array $ticketData
     * @return void
     * @throws LocalizedException
     */
    public function execute($ticketData)
    {
        $dataProvider = $this->requestDataProvider->prepareMergeData($ticketData);
        $ticketsToMerge = $dataProvider->getTicketsToMerge();
        $mainTicket = $dataProvider->getMainTicket();

        if ($mainTicket && $ticketsToMerge) {
            $this->mergeHandler->merge($mainTicket, $ticketsToMerge);
        }
    }
}
