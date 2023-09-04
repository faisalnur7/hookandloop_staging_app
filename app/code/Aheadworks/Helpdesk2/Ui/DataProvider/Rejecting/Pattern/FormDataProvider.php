<?php
namespace Aheadworks\Helpdesk2\Ui\DataProvider\Rejecting\Pattern;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Aheadworks\Helpdesk2\Model\Rejecting\Pattern as RejectingPatternModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectingPattern\Collection;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectingPattern\CollectionFactory;

/**
 * Class FormDataProvider
 *
 * @package Aheadworks\Helpdesk2\Ui\DataProvider\Rejecting\Pattern
 */
class FormDataProvider extends AbstractDataProvider
{
    /**
     * Key for saving and getting form data from data persistor
     */
    const DATA_PERSISTOR_FORM_DATA_KEY = 'aw_helpdesk2_rejecting_pattern';

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
            $id = $dataFromForm[RejectingPatternInterface::ID] ?? null;
            $this->dataPersistor->clear(self::DATA_PERSISTOR_FORM_DATA_KEY);
            $preparedData[$id] = $dataFromForm;
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            $patternList = $this
                ->getCollection()
                ->addFieldToFilter(RejectingPatternInterface::ID, $id)
                ->getItems();
            /** @var RejectingPatternModel $pattern */
            foreach ($patternList as $pattern) {
                if ($id == $pattern->getId()) {
                    $preparedData[$id] = $pattern->getData();
                }
            }
        }

        return $preparedData;
    }
}
