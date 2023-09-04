<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Import;

use Aheadworks\Blog\Model\Import\Processor\Composite;
use Magento\Backend\App\Action\Context;

/**
 * Class Import
 */
class Start extends \Magento\Backend\App\Action
{
    /**
     * @var Composite
     */
    private $compositeImport;

    /**
     * Import constructor.
     * @param Context $context
     * @param Composite $compositeImport
     */
    public function __construct(
        Context $context,
        Composite $compositeImport
    ) {
        parent::__construct($context);
        $this->compositeImport = $compositeImport;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        try {
            $this->compositeImport->perform($data);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Sorry, but the data is invalid or the file is not uploaded.'));
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}