<?php
namespace Aheadworks\Helpdesk2\Model\Rejection\Processor\Type;

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Rejection\Processor\ProcessorInterface;

/**
 * Class ContactUsForm
 *
 * @package Aheadworks\Helpdesk2\Model\Rejection\Processor\Type
 */
class ContactUsForm implements ProcessorInterface
{
    /**
     * @var CommandInterface
     */
    private $createCommand;

    /**
     * @param CommandInterface $createCommand
     */
    public function __construct(
        CommandInterface $createCommand
    ) {
        $this->createCommand = $createCommand;
    }

    /**
     * @inheritDoc
     */
    public function process($rejectedMessage)
    {
        $ticketData = $rejectedMessage->getMessageData();
        $this->createCommand->execute($ticketData);

        return true;
    }
}
