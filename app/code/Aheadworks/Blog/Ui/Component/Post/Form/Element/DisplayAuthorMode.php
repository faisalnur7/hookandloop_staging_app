<?php
namespace Aheadworks\Blog\Ui\Component\Post\Form\Element;

use Aheadworks\Blog\Model\Config;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;

/**
 * Class DisplayAuthorMode
 */
class DisplayAuthorMode extends Field
{
    /**
     * @var Config
     */
    private $config;

    /**
     * DisplayAuthorMode constructor.
     * @param Config $config
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        Config $config,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        $config['default'] = (string)(int)$this->config->areAuthorsDisplayed();
        $this->setData('config', $config);

        parent::prepare();
    }
}
