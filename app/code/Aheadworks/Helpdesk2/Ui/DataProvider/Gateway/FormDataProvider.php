<?php
namespace Aheadworks\Helpdesk2\Ui\DataProvider\Gateway;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\Gateway as GatewayModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Collection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\CollectionFactory;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;

/**
 * Class FormDataProvider
 *
 * @package Aheadworks\Helpdesk2\Ui\DataProvider\Gateway
 */
class FormDataProvider extends AbstractDataProvider
{
    /**
     * Key for saving and getting form data from data persistor
     */
    const DATA_PERSISTOR_FORM_DATA_KEY = 'aw_helpdesk2_gateway';

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ProcessorInterface
     */
    private $formDataProcessor;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
     * @param ProcessorInterface $formDataProcessor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        ProcessorInterface $formDataProcessor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
        $this->formDataProcessor = $formDataProcessor;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $preparedData = [];
        $dataFromForm = $this->dataPersistor->get(self::DATA_PERSISTOR_FORM_DATA_KEY);

        if (!empty($dataFromForm) && (is_array($dataFromForm))) {
            $id = $dataFromForm[GatewayDataInterface::ID] ?? null;
            $this->dataPersistor->clear(self::DATA_PERSISTOR_FORM_DATA_KEY);
            $preparedData[$id] = $dataFromForm;
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            $gatewayList = $this->getCollection()->addFieldToFilter(GatewayDataInterface::ID, $id)->getItems();
            /** @var GatewayModel $gateway */
            foreach ($gatewayList as $gateway) {
                if ($id == $gateway->getId()) {
                    $preparedData[$id] = $this->formDataProcessor->prepareEntityData($gateway->getData());
                }
            }
        }

        return $preparedData;
    }

    /**
     * @inheritdoc
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        $meta = $this->formDataProcessor->prepareMetaData($meta);

        return $meta;
    }
}
