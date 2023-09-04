<?php
namespace Aheadworks\Helpdesk2\Ui\Component\Form\Element;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Container;

/**
 * Class DynamicRows
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Form\Element
 */
class DynamicRows extends Container
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ContextInterface $context
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        if ($this->storeManager->hasSingleStore()) {
            $config['addButton'] = false;
            $this->setData('config', $config);
        }
        parent::prepare();
    }
}
