<?php

namespace Magecomp\Ordercomment\Ui\Component\Listing\Column;

class Managecolumn extends \Magento\Ui\Component\Listing\Columns\Column
{
    protected $statuses;

   /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CollectionFactory $collectionFactory
     * @param array $components
     * @param array $data
     */
       public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
         parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    /**
     * @param array $dataSource
     * @return void
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) 
        {
            foreach ($dataSource['data']['items'] as & $item)
             {
                if($item['magecomp_order_comment'])
                {
                    $item['magecomp_order_comment']=substr($item['magecomp_order_comment'], 0, 20).'...';
                }
            }
        }
        return $dataSource;
   }
}