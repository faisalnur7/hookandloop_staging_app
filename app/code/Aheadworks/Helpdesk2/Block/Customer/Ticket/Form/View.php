<?php
namespace Aheadworks\Helpdesk2\Block\Customer\Ticket\Form;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\ProcessorInterface;
use Aheadworks\Helpdesk2\ViewModel\Ticket\View as TicketViewModel;

/**
 * Class View
 *
 * @method TicketViewModel getTicketViewModel()
 * @package Aheadworks\Helpdesk2\Block\Customer\Ticket\Form
 */
class View extends Template
{
    /**
     * @inheritdoc
     */
    protected $_template = 'Aheadworks_Helpdesk2::customer/ticket/form.phtml';

    /**
     * @var ProcessorInterface
     */
    private $layoutProcessor;

    /**
     * @param Context $context
     * @param ProcessorInterface $layoutProcessor
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProcessorInterface $layoutProcessor,
        array $data = []
    ) {
        $this->layoutProcessor = $layoutProcessor;
        parent::__construct($context, $data);
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout'])
            ? $data['jsLayout']
            : [];
    }

    /**
     * @inheritdoc
     *
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getJsLayout()
    {
        $view = $this->getTicketViewModel();
        $this->jsLayout = $this->layoutProcessor->process(
            $this->jsLayout,
            $view->getTicketViewRenderer()
        );

        return parent::getJsLayout();
    }
}
