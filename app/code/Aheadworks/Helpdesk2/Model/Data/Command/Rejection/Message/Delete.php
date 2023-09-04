<?php
namespace Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Message;

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Rejection\MessageRepository;

/**
 * Class Delete
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Email
 */
class Delete implements CommandInterface
{
    /**
     * @var MessageRepository
     */
    private $rejectedMessageRepository;

    /**
     * @param MessageRepository $rejectedMessageRepository
     */
    public function __construct(
        MessageRepository $rejectedMessageRepository
    ) {
        $this->rejectedMessageRepository = $rejectedMessageRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data['item'])) {
            throw new \InvalidArgumentException(
                'Rejected email item param is required'
            );
        }

        $this->rejectedMessageRepository->delete($data['item']);

        return true;
    }
}
