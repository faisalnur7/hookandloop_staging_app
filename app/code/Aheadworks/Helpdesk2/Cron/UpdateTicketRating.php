<?php
namespace Aheadworks\Helpdesk2\Cron;

use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotSaveException;
use Aheadworks\Helpdesk2\Model\Flag;
use Aheadworks\Helpdesk2\Model\Ticket\Rating\Updater as RatingUpdater;

/**
 * Class UpdateTicketRating
 *
 * @package Aheadworks\Helpdesk2\Cron
 */
class UpdateTicketRating
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Management
     */
    private $cronManagement;

    /**
     * @var RatingUpdater
     */
    private $ratingUpdater;

    /**
     * @param LoggerInterface $logger
     * @param Management $cronManagement
     * @param RatingUpdater $ratingUpdater
     */
    public function __construct(
        LoggerInterface $logger,
        Management $cronManagement,
        RatingUpdater $ratingUpdater
    ) {
        $this->logger = $logger;
        $this->cronManagement = $cronManagement;
        $this->ratingUpdater = $ratingUpdater;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     * @throws CouldNotSaveException
     */
    public function execute()
    {
        if (!$this->cronManagement->isLocked(Flag::AW_HELPDESK2_UPDATE_TICKET_RATING_EXEC_TIME)) {
            try {
                $this->ratingUpdater->update();
            } catch (\LogicException $e) {
                $this->logger->error($e);
            }
            $this->cronManagement->setFlagData(Flag::AW_HELPDESK2_UPDATE_TICKET_RATING_EXEC_TIME);
        }
    }
}
