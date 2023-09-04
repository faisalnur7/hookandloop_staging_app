<?php

/*
 * @category   AmazonProductMapping
 * @author     pawan 
 * @copyright  Exinent_AmazonProductMapping
 * 
 */

namespace Exinent\AmazonProductMapping\Model\Cron;

class CustomReindexStockData {

    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    protected $_indexerFactory;

    /**
     * @var \Magento\Indexer\Model\Indexer\CollectionFactory
     */
    protected $_indexerCollectionFactory;

    public function __construct(
        \Magento\Indexer\Model\IndexerFactory $indexerFactory, 
        \Magento\Indexer\Model\Indexer\CollectionFactory $indexerCollectionFactory
    ) {
        $this->_indexerFactory = $indexerFactory;
        $this->_indexerCollectionFactory = $indexerCollectionFactory;
    }

// you can call this function to do reindexing
    public function reIndexing() {
        $indexerCollection = $this->_indexerCollectionFactory->create();
        $ids = $indexerCollection->getAllIds();
        foreach ($ids as $id) {
            $idx = $this->_indexerFactory->create()->load($id);
            $idx->reindexAll($id); // this reindexes all
            //$idx->reindexRow($id); // or you can use reindexRow according to your need
        }
    }
}
    