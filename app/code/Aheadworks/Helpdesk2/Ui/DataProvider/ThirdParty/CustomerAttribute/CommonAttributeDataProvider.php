<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Ui\DataProvider\ThirdParty\CustomerAttribute;

use Magento\Framework\Api\Filter;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\ThirdPartyModule\ModuleChecker;

/**
 * Class CommonAttributeDataProvider
 */
class CommonAttributeDataProvider extends AbstractDataProvider implements DataProviderInterface
{
    /**
     * @var ProcessorInterface
     */
    private $activeFormDataProcessor;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param RequestInterface $request
     * @param ModuleChecker $moduleChecker
     * @param array $formDataProcessors
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        private RequestInterface $request,
        private ModuleChecker $moduleChecker,
        private array $formDataProcessors,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData(): array
    {
        $preparedData = [];

        $ticketId = $this->request->getParam($this->getRequestFieldName());
        if ($ticketId) {
            $activeFormDataProcessor = $this->getActiveFormDataProcessor();
            if ($activeFormDataProcessor) {
                $preparedData[$ticketId] = $activeFormDataProcessor->prepareEntityData(['ticket_id' => $ticketId]);
            }
        }

        return $preparedData;
    }

    /**
     * Add field filter to collection
     *
     * @param \Magento\Framework\Api\Filter $filter
     * @return mixed
     */
    public function addFilter(Filter $filter)
    {
        return $this;
    }

    /**
     * Get meta
     *
     * @return array
     */
    public function getMeta(): array
    {
        $meta = parent::getMeta();
        $activeFormDataProcessor = $this->getActiveFormDataProcessor();
        if ($activeFormDataProcessor) {
            $meta = $activeFormDataProcessor->prepareMetaData($meta);
        }
        return $meta;
    }

    /**
     * Get active form data processor
     *
     * @return ProcessorInterface|null
     */
    private function getActiveFormDataProcessor(): ?ProcessorInterface
    {
        if (!$this->activeFormDataProcessor) {
            $isAwCustomerAttributeEnabled = $this->moduleChecker->isAwCustomerAttributesEnabled();
            $isMagentoCustomerAttributeEnabled = $this->moduleChecker->isCustomerCustomAttributesEnabled();
            if ($isAwCustomerAttributeEnabled) {
                $this->activeFormDataProcessor = $this->formDataProcessors['awCustomerAttributes'];
            } elseif ($isMagentoCustomerAttributeEnabled) {
                $this->activeFormDataProcessor = $this->formDataProcessors['magentoCustomerAttributes'];
            }
        }
        return $this->activeFormDataProcessor;
    }
}
