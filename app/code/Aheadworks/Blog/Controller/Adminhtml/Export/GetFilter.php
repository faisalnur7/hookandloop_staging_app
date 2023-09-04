<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Aheadworks\Blog\Model\Export;

/**
 * Class GetFilter
 */
class GetFilter extends \Magento\ImportExport\Controller\Adminhtml\Export\GetFilter
{
    /**
     * @var Export
     */
    private $export;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * GetFilter constructor.
     * @param Action\Context $context
     * @param Export $export
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Action\Context $context,
        Export $export,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->export = $export;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Get grid-filter of entity attributes action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $messages = [];

        if ($this->getRequest()->isXmlHttpRequest() && $data) {
            try {
                /** @var \Magento\Framework\View\Result\Layout $resultLayout */
                $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);

                /** @var $attrFilterBlock \Magento\ImportExport\Block\Adminhtml\Export\Filter */
                $attrFilterBlock = $resultLayout->getLayout()->getBlock('export.filter');
                $this->export->setData($data);

                $this->export->filterAttributeCollection(
                    $attrFilterBlock->prepareCollection($this->export->getEntityAttributeCollection())
                );

                return $resultLayout;
            } catch (\Exception $e) {
                $messages['error'] = __($e->getMessage());
            }
        } else {
            $messages['error'] = __('Please correct the data sent value.');
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($messages);
    }
}