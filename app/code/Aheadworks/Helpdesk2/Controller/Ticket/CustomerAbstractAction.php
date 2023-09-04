<?php
namespace Aheadworks\Helpdesk2\Controller\Ticket;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Controller\TicketAbstract;

/**
 * Class CustomerAbstractAction
 *
 * @package Aheadworks\Helpdesk2\Controller\Ticket
 */
abstract class CustomerAbstractAction extends TicketAbstract
{
    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }

    /**
     * Check if ticket is belonging to logged in customer
     *
     * @param TicketInterface $ticket
     * @return bool
     */
    protected function isTicketBelongToCurrentCustomer($ticket)
    {
        $customer = $this->customerSession->getCustomer();
        if ($customer) {
            if (($ticket->getCustomerId() && $ticket->getCustomerId() == $customer->getId())
                || ($ticket->getCustomerEmail() == $customer->getEmail())) {
                return true;
            }
        }

        return false;
    }
}
