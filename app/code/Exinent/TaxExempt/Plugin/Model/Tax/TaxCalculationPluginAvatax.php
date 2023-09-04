<?php

namespace Exinent\TaxExempt\Plugin\Model\Tax;

use Magento\Framework\Session\SessionManagerInterface;
use Exception;
use ClassyLlama\AvaTax\Framework\Interaction\TaxCalculation;

/**
 * Class TaxCalculationPlugin
 * @package Exinent\TaxExempt\Plugin\Model\Tax
 */
class TaxCalculationPluginAvatax
{

    protected $_coreSession;
    protected $_productloader;

    public function __construct(
    SessionManagerInterface $coreSession
    ) {
        $this->_coreSession = $coreSession;
    }

    /**
     * Save Extension attributes for customer
     *
     * @param TaxCalculationInterface $subject
     * @param $result
     * @param $storeId
     * @return $result
     */
    public function afterCalculateTaxDetails(
        TaxCalculation $subject,
        $result,
        $quoteDetails,
        $getTaxResult,
        $useBaseCurrency,
        $scope
    ) {
        try {
            $quoteData = $quoteDetails->getShippingAddress()->__toArray();
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
