<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Post\Save;

/**
 * Interceptor class for @see \Aheadworks\Blog\Controller\Adminhtml\Post\Save
 */
class Interceptor extends \Aheadworks\Blog\Controller\Adminhtml\Post\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Aheadworks\Blog\Model\Data\Processor\ProcessorInterface $postDataProcessor, \Aheadworks\Blog\Api\PostRepositoryInterface $postRepository, \Aheadworks\Blog\Api\Data\PostInterfaceFactory $postDataFactory, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor)
    {
        $this->___init();
        parent::__construct($context, $postDataProcessor, $postRepository, $postDataFactory, $dataObjectHelper, $dataPersistor);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        return $pluginInfo ? $this->___callPlugins('execute', func_get_args(), $pluginInfo) : parent::execute();
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        return $pluginInfo ? $this->___callPlugins('dispatch', func_get_args(), $pluginInfo) : parent::dispatch($request);
    }
}
