<?php

namespace Exinent\TaxExempt\Plugin\Model\Tax;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Catalog\Model\ProductFactory; 
use Exception;
use Magento\Tax\Api\TaxCalculationInterface;

/**
 * Class TaxCalculationPlugin
 * @package Exinent\TaxExempt\Plugin\Model\Tax
 */
class TaxCalculationPlugin
{

    protected $_coreSession;
    protected $_productloader;

    public function __construct(
    SessionManagerInterface $coreSession, ProductFactory $_productloader
    ) {
        $this->_coreSession = $coreSession;
        $this->_productloader = $_productloader;
    }

    /**
     * Save Extension attributes for customer
     *
     * @param TaxCalculationInterface $subject
     * @param $result
     * @param $storeId
     * @return $result
     */
    public function afterCalculateTax(
        TaxCalculationInterface $subject,
        $result,
        $quoteDetails
    ) {
        try {
            //$quoteData = $quoteDetails->getShippingAddress()->__toArray();
            $taxcode = $this->_coreSession->getTaxReliefCode();
            $region = $this->_coreSession->getTaxReliefState(); 
            if(!empty($taxcode) && $region != 'Please select region, state or province') {
                $result->setTaxAmount(0.00);
                $result->setAppliedTaxes([]);

                $processedItems = $result->getItems();

                foreach ($processedItems as $data) {
                    $data->setPriceInclTax($data->getPrice());
                    $data->setRowTotalInclTax($data->getRowTotal());
                    $data->setRowTax(0.00);
                    $data->setTaxPercent(0);
                    $data->setAppliedTaxes([]);
                }
            }
        } catch (Exception $e) {
        }

        return $result;
    }
}
