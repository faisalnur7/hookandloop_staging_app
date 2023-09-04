<?php
namespace Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Aheadworks\Helpdesk2\Model\Ticket\GridInterface;

/**
 * Class SubjectColumn
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket\Columns
 */
class SubjectColumn extends Column
{
    /**
     * @var DataFormatter
     */
    private $dataFormatter;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param DataFormatter $dataFormatter
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        DataFormatter $dataFormatter,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->dataFormatter = $dataFormatter;
    }

    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as &$item) {
            $item[GridInterface::LAST_MESSAGE_CONTENT] = $this->dataFormatter->prepareMessageContent($item);
            $item[GridInterface::LAST_MESSAGE_DATE . '_formatted'] = $this->dataFormatter->prepareMessageDate($item);
        }

        return $dataSource;
    }
}
