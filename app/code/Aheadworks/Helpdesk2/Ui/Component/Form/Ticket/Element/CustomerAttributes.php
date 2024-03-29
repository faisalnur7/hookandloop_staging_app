<?php
namespace Aheadworks\Helpdesk2\Ui\Component\Form\Ticket\Element;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\Helpdesk2\Model\ThirdPartyModule\ModuleChecker as ThirdPartyModuleChecker;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;

/**
 * Class CustomerAttributes
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Form\Ticket\Element
 */
class CustomerAttributes extends \Magento\Ui\Component\Form\Fieldset
{
    /**
     * @var ThirdPartyModuleChecker
     */
    private $thirdPartyModuleChecker;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param ContextInterface $context
     * @param ThirdPartyModuleChecker $thirdPartyModuleChecker
     * @param TicketRepositoryInterface $ticketRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        ThirdPartyModuleChecker $thirdPartyModuleChecker,
        TicketRepositoryInterface $ticketRepository,
        CustomerRepositoryInterface $customerRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->thirdPartyModuleChecker = $thirdPartyModuleChecker;
        $this->ticketRepository = $ticketRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function prepare()
    {
        parent::prepare();

        if (!$this->isComponentVisible()) {
            $config = $this->getData('config');
            $config['disabled'] = true;
            $config['visible'] = false;
            $this->setData('config', $config);
        }
    }

    /**
     * Check if component is visible
     *
     * @return bool
     * @throws LocalizedException
     */
    private function isComponentVisible()
    {
        $result = false;
        if ($this->thirdPartyModuleChecker->isAwCustomerAttributesEnabled() ||
            $this->thirdPartyModuleChecker->isCustomerCustomAttributesEnabled()) {
            $ticketId = $this->getContext()->getRequestParam(TicketInterface::ENTITY_ID);
            try {
                $ticket = $this->ticketRepository->getById($ticketId);
                $this->customerRepository->get($ticket->getCustomerEmail());
                $result = true;
            } catch (NoSuchEntityException $exception) {
                $result = false;
            }
        }

        return $result;
    }
}
