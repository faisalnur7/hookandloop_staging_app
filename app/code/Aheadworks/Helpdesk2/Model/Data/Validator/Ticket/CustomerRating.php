<?php
namespace Aheadworks\Helpdesk2\Model\Data\Validator\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\CustomerRating as CustomerRatingSource;
use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;

/**
 * Class CustomerRating
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Validator\Ticket
 */
class CustomerRating extends AbstractValidator
{
    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @param DepartmentRepositoryInterface $departmentRepository
     */
    public function __construct(
        DepartmentRepositoryInterface $departmentRepository
    ) {
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * Check if agent is correct
     *
     * @param TicketInterface $ticket
     * @return bool
     * @throws \Exception
     */
    public function isValid($ticket)
    {
        $this->_clearMessages();

        $rating = $ticket->getCustomerRating();
        if (isset($rating) && !$this->isRatingValueCorrect($rating)) {
            $this->_addMessages([__('Invalid Customer Rating value')]);
        }

        return empty($this->getMessages());
    }

    /**
     * Is rating value correct
     *
     * @param string|int $rating
     * @return bool
     */
    private function isRatingValueCorrect($rating)
    {
        return is_numeric($rating)
            && (int)$rating <= CustomerRatingSource::MAX_VALUE
            && (int)$rating >= 0;
    }
}
