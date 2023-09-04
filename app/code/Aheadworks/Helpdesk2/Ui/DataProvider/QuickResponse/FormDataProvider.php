<?php
namespace Aheadworks\Helpdesk2\Ui\DataProvider\QuickResponse;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;
use Aheadworks\Helpdesk2\Model\QuickResponse as QuickResponseModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\QuickResponse\Collection;
use Aheadworks\Helpdesk2\Model\ResourceModel\QuickResponse\CollectionFactory;

/**
 * Class FormDataProvider
 *
 * @package Aheadworks\Helpdesk2\Ui\DataProvider\QuickResponse
 */
class FormDataProvider extends AbstractDataProvider
{
    /**
     * Key for saving and getting form data from data persistor
     */
    const DATA_PERSISTOR_FORM_DATA_KEY = 'aw_helpdesk2_quick_response';

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
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
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
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $preparedData = [];
        $dataFromForm = $this->dataPersistor->get(self::DATA_PERSISTOR_FORM_DATA_KEY);

        if (!empty($dataFromForm) && (is_array($dataFromForm))) {
            $id = $dataFromForm[QuickResponseInterface::ID] ?? null;
            $this->dataPersistor->clear(self::DATA_PERSISTOR_FORM_DATA_KEY);
            $preparedData[$id] = $dataFromForm;
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            $quickResponseList = $this->getCollection()->addFieldToFilter(QuickResponseInterface::ID, $id)->getItems();
            /** @var QuickResponseModel $quickResponse */
            foreach ($quickResponseList as $quickResponse) {
                if ($id == $quickResponse->getId()) {
                    $preparedData[$id] = $quickResponse->getData();
                }
            }
        }

        return $preparedData;
    }
}
