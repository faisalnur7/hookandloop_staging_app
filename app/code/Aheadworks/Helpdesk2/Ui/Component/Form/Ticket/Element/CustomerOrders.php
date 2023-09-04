<?php
namespace Aheadworks\Helpdesk2\Ui\Component\Form\Ticket\Element;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class CustomerOrders
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Form\Ticket\Element
 */
class CustomerOrders extends \Magento\Ui\Component\Form\Fieldset
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @param ContextInterface $context
     * @param AuthorizationInterface $authorization
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        AuthorizationInterface $authorization,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->authorization = $authorization;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();

        if (!$this->authorization->isAllowed('Magento_Sales::sales_order')) {
            $config = $this->getData('config');
            $config['disabled'] = true;
            $config['visible'] = false;
            $this->setData('config', $config);
        }
    }
}
