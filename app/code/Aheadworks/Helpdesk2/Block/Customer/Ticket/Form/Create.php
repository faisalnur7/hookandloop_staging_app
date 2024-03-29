<?php
namespace Aheadworks\Helpdesk2\Block\Customer\Ticket\Form;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\ProcessorInterface;
use Aheadworks\Helpdesk2\ViewModel\Ticket\Creation;

/**
 * Class Create
 *
 * @method Creation getCreationViewModel()
 * @package Aheadworks\Helpdesk2\Block\Customer\Ticket\Form
 */
class Create extends Template
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
        $creationViewModel = $this->getCreationViewModel();
        $creationRenderer = $creationViewModel->getTicketCreationRenderer();
        $creationRenderer->setRequest($this->getRequest());

        $this->jsLayout = $this->layoutProcessor->process(
            $this->jsLayout,
            $creationRenderer
        );

        return parent::getJsLayout();
    }
}
