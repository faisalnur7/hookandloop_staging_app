<?php

/**
 * Copyright © 2017 Magento. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\CronScheduler\Ui\DataProvider;

/**
 * Job provider for the jobs listing
 * @version 1.0.0
 */
class JobProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var int
     */
    protected $_size = 20;
    /**
     * @var int
     */
    protected $_offset = 1;
    /**
     * @var array
     */
    protected $_likeFilters = [];
    /**
     * @var array
     */
    protected $_rangeFilters = [];
    /**
     * @var string
     */
    protected $_sortField = 'code';
    /**
     * @var string
     */
    protected $_sortDir = 'asc';
    public $_directoryList = null;
    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    private $_directoryRead = null;
    public $jobHelper = null;
    public function __construct(
        \Wyomind\CronScheduler\Helper\Delegate $wyomind,
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Filesystem\Directory\ReadFactory $directoryRead,
        /** @delegation off */
        array $meta = [],
        array $data = []
    )
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        $this->_directoryRead = $directoryRead;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
    /**
     * Set the limit of the collection
     * @param int $offset
     * @param int $size
     */
    public function setLimit($offset, $size)
    {
        $this->_size = $size;
        $this->_offset = $offset;
    }
    /**
     * Get the collection
     * @return array
     */
    public function getData()
    {
        $data = array_values($this->jobHelper->getJobData());
        $totalRecords = count($data);
        // sorting
        $sortField = $this->_sortField;
        $sortDir = $this->_sortDir;
        usort($data, function ($a, $b) use($sortField, $sortDir) {
            if ($sortDir == "asc") {
                return $a[$sortField] > $b[$sortField] ? 1 : 0;
            } else {
                return $a[$sortField] < $b[$sortField] ? 1 : 0;
            }
        });
        // filters
        foreach ($this->_likeFilters as $column => $value) {
            $data = array_filter($data, function ($item) use($column, $value) {
                return stripos($item[$column], $value) !== false;
            });
        }
        // pagination
        $data = array_slice($data, ($this->_offset - 1) * $this->_size, $this->_size);
        return ['totalRecords' => $totalRecords, 'items' => $data];
    }
    /**
     * Add filters to the collection
     * @param \Magento\Framework\Api\Filter $filter
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getConditionType() == "like") {
            $this->_likeFilters[$filter->getField()] = substr($filter->getValue(), 1, -1);
        } elseif ($filter->getConditionType() == "eq") {
            $this->_likeFilters[$filter->getField()] = $filter->getValue();
        } elseif ($filter->getConditionType() == "gteq") {
            $this->_rangeFilters[$filter->getField()]['from'] = $filter->getValue();
        } elseif ($filter->getConditionType() == "lteq") {
            $this->_rangeFilters[$filter->getField()]['to'] = $filter->getValue();
        }
    }
    /**
     * Set the order of the collection
     * @param string $field
     * @param string $direction
     */
    public function addOrder($field, $direction)
    {
        $this->_sortField = $field;
        $this->_sortDir = strtolower($direction);
    }
}
