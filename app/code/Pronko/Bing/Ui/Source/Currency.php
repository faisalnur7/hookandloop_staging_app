<?php
/**
 * Copyright © Pronko Consulting (https://www.pronkoconsulting.com)
 * See LICENSE for the license details.
 */
namespace Pronko\Bing\Ui\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\Locale\ListsInterface;
use Magento\Store\Model\StoreManagerInterface;

class Currency implements ArrayInterface
{
    /**
     * @var ListsInterface
     */
    private $localeLists;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Currency constructor.
     * @param ListsInterface $localeLists
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ListsInterface $localeLists,
        StoreManagerInterface $storeManager
    ) {
        $this->localeLists = $localeLists;
        $this->storeManager = $storeManager;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function toOptionArray()
    {
        $result = [];
        $availableCurrencyCodes = $this->storeManager->getStore()->getAvailableCurrencyCodes();
        foreach ($this->localeLists->getOptionCurrencies() as $optionCurrency) {
            if (in_array($optionCurrency['value'], $availableCurrencyCodes)) {
                $result[] = $optionCurrency;
            }
        }
        return $result;
    }
}
