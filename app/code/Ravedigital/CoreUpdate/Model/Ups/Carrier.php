<?php
namespace Ravedigital\CoreUpdate\Model\Ups;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Framework\DataObject;
use Magento\Sales\Model\Order\Shipment as OrderShipment;
use Magento\Store\Model\ScopeInterface;

class Carrier extends \Magento\Ups\Model\Carrier
{
    public function setRequest(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        $this->_request = $request;

        $rowRequest = new \Magento\Framework\DataObject();
        $fetchItemsweight = 0;

        if ($request->getLimitMethod()) {
            $rowRequest->setAction($this->configHelper->getCode('action', 'single'));
            $rowRequest->setProduct($request->getLimitMethod());
        } else {
            $rowRequest->setAction($this->configHelper->getCode('action', 'all'));
            $rowRequest->setProduct('GND' . $this->getConfigData('dest_type'));
        }

        if ($request->getUpsPickup()) {
            $pickup = $request->getUpsPickup();
        } else {
            $pickup = $this->getConfigData('pickup');
        }
        $rowRequest->setPickup($this->configHelper->getCode('pickup', $pickup));

        if ($request->getUpsContainer()) {
            $container = $request->getUpsContainer();
        } else {
            $container = $this->getConfigData('container');
        }
        $rowRequest->setContainer($this->configHelper->getCode('container', $container));

        if ($request->getUpsDestType()) {
            $destType = $request->getUpsDestType();
        } else {
            $destType = $this->getConfigData('dest_type');
        }
        $rowRequest->setDestType($this->configHelper->getCode('dest_type', $destType));

        if ($request->getOrigCountry()) {
            $origCountry = $request->getOrigCountry();
        } else {
            $origCountry = $this->_scopeConfig->getValue(
                \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_COUNTRY_ID,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $request->getStoreId()
            );
        }

        $rowRequest->setOrigCountry($this->_countryFactory->create()->load($origCountry)->getData('iso2_code'));

        if ($request->getOrigRegionCode()) {
            $origRegionCode = $request->getOrigRegionCode();
        } else {
            $origRegionCode = $this->_scopeConfig->getValue(
                \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_REGION_ID,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $request->getStoreId()
            );
        }
        if (is_numeric($origRegionCode)) {
            $origRegionCode = $this->_regionFactory->create()->load($origRegionCode)->getCode();
        }
        $rowRequest->setOrigRegionCode($origRegionCode);

        if ($request->getOrigPostcode()) {
            $rowRequest->setOrigPostal($request->getOrigPostcode());
        } else {
            $rowRequest->setOrigPostal(
                $this->_scopeConfig->getValue(
                    \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ZIP,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStoreId()
                )
            );
        }

        if ($request->getOrigCity()) {
            $rowRequest->setOrigCity($request->getOrigCity());
        } else {
            $rowRequest->setOrigCity(
                $this->_scopeConfig->getValue(
                    \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_CITY,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStoreId()
                )
            );
        }

        if ($request->getDestCountryId()) {
            $destCountry = $request->getDestCountryId();
        } else {
            $destCountry = self::USA_COUNTRY_ID;
        }

        //for UPS, puero rico state for US will assume as puerto rico country
        if ($destCountry == self::USA_COUNTRY_ID && ($request->getDestPostcode() == '00912' ||
           $request->getDestRegionCode() == self::PUERTORICO_COUNTRY_ID)
        ) {
            $destCountry = self::PUERTORICO_COUNTRY_ID;
        }

        // For UPS, Guam state of the USA will be represented by Guam country
        if ($destCountry == self::USA_COUNTRY_ID && $request->getDestRegionCode() == self::GUAM_REGION_CODE) {
            $destCountry = self::GUAM_COUNTRY_ID;
        }

        // For UPS, Las Palmas and Santa Cruz de Tenerife will be represented by Canary Islands country
        if ($destCountry === 'ES' &&
           ($request->getDestRegionCode() === 'Las Palmas'
               || $request->getDestRegionCode() === 'Santa Cruz de Tenerife')
        ) {
            $destCountry = 'IC';
        }

        $country = $this->_countryFactory->create()->load($destCountry);
        $rowRequest->setDestCountry($country->getData('iso2_code') ?: $destCountry);

        $rowRequest->setDestRegionCode($request->getDestRegionCode());

        if ($request->getDestPostcode()) {
            $rowRequest->setDestPostal($request->getDestPostcode());
        }

        $allItems = $this->_request->getAllItems();
        
        foreach ($allItems as $item) {
            $itemWeight = $item->getProduct()->getWeight();
            $fetchItemsweight += $itemWeight * $item->getQty();
        }

        $weight = $this->getTotalNumOfBoxes($fetchItemsweight);
        $weight = $this->_getCorrectWeight($weight);

        $rowRequest->setWeight($weight);
    
        $rowRequest->setOrigCountry(
            $this->getNormalizedCountryCode(
                $rowRequest->getOrigCountry(),
                $rowRequest->getOrigRegionCode(),
                $rowRequest->getOrigPostal()
            )
        );

        $rowRequest->setDestCountry(
            $this->getNormalizedCountryCode(
                $rowRequest->getDestCountry(),
                $rowRequest->getDestRegionCode(),
                $rowRequest->getDestPostal()
            )
        );

        if ($request->getFreeMethodWeight() != $request->getPackageWeight()) {
            $rowRequest->setFreeMethodWeight($request->getFreeMethodWeight());
        }

        $rowRequest->setPackages(
            $this->createPackages(
                (float) $request->getPackageWeight(),
                (array) $request->getPackages()
            )
        );
        $rowRequest->setWeight($this->_getCorrectWeight($request->getPackageWeight()));
        $rowRequest->setValue($request->getPackageValue());
        $rowRequest->setValueWithDiscount($request->getPackageValueWithDiscount());

        if ($request->getUpsUnitMeasure()) {
            $unit = $request->getUpsUnitMeasure();
        } else {
            $unit = $this->getConfigData('unit_of_measure');
        }
        $rowRequest->setUnitMeasure($unit);
        $rowRequest->setIsReturn($request->getIsReturn());
        $rowRequest->setBaseSubtotalInclTax($request->getBaseSubtotalInclTax());
        $rowRequest->setDestCity($request->getDestCity());
        $this->_rawRequest = $rowRequest;

        return $this;
    }

    /**
     * Creates packages for rate request.
     *
     * @param float $totalWeight
     * @param array $packages
     * @return array
     */
    private function createPackages(float $totalWeight, array $packages): array
    {
        if (empty($packages)) {
            $dividedWeight = $this->getTotalNumOfBoxes($totalWeight);
            for ($i=0; $i < $this->_numBoxes; $i++) {
                $packages[$i]['weight'] = $this->_getCorrectWeight($dividedWeight);
            }
        }
        $this->_numBoxes = count($packages);

        return $packages;
    }

    /**
     * Return country code according to UPS
     *
     * @param string $countryCode
     * @param string $regionCode
     * @param string $postCode
     * @return string
     */
    private function getNormalizedCountryCode($countryCode, $regionCode, $postCode)
    {
        //for UPS, puerto rico state for US will assume as puerto rico country
        if ($countryCode == self::USA_COUNTRY_ID
            && ($postCode == '00912'
                || $regionCode == self::PUERTORICO_COUNTRY_ID)
        ) {
            $countryCode = self::PUERTORICO_COUNTRY_ID;
        }

        // For UPS, Guam state of the USA will be represented by Guam country
        if ($countryCode == self::USA_COUNTRY_ID && $regionCode == self::GUAM_REGION_CODE) {
            $countryCode = self::GUAM_COUNTRY_ID;
        }

        // For UPS, Las Palmas and Santa Cruz de Tenerife will be represented by Canary Islands country
        if ($countryCode === 'ES' &&
            ($regionCode === 'Las Palmas'
                || $regionCode === 'Santa Cruz de Tenerife')
        ) {
            $countryCode = 'IC';
        }

        return $countryCode;
    }

    /**
     * Prepare shipping rate result based on response
     *
     * @param mixed $xmlResponse
     * @return Result
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    // protected function _parseXmlResponse($xmlResponse)
    // {
    //     $costArr = [];
    //     $priceArr = [];
    //     $writer= new \Zend\Log\Writer\Stream(BP . '/var/log/carrier.log');
    //     $logger= new \Zend\Log\Logger();
    //     $logger->addWriter($writer);
    //     // $logger->info('UPS response');
    //     // $logger->info($xmlResponse);
    //     if (strlen(trim($xmlResponse)) > 0) {
    //         $xml = new \Magento\Framework\Simplexml\Config();
    //         $xml->loadString($xmlResponse);
    //         $arr = $xml->getXpath("//RatingServiceSelectionResponse/Response/ResponseStatusCode/text()");
    //         $success = (int)$arr[0];
    //         if ($success === 1) {
    //             $arr = $xml->getXpath("//RatingServiceSelectionResponse/RatedShipment");
    //             $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));

    //             // Negotiated rates
    //             $negotiatedArr = $xml->getXpath("//RatingServiceSelectionResponse/RatedShipment/NegotiatedRates");
    //             $negotiatedActive = $this->getConfigFlag('negotiated_active')
    //                 && $this->getConfigData('shipper_number')
    //                 && !empty($negotiatedArr);
    //             $etaArr = array();
    //             $allowedCurrencies = $this->_currencyFactory->create()->getConfigAllowCurrencies();
    //             foreach ($arr as $shipElement) {
    //                 $this->processShippingRateForItem(
    //                     $shipElement,
    //                     $allowedMethods,
    //                     $allowedCurrencies,
    //                     $costArr,
    //                     $priceArr,
    //                     $negotiatedActive,
    //                     $xml,
    //                     $etaArr
    //                 );
    //             }
    //         } else {
    //             $arr = $xml->getXpath("//RatingServiceSelectionResponse/Response/Error/ErrorDescription/text()");
    //             $errorTitle = (string)$arr[0][0];
    //             $error = $this->_rateErrorFactory->create();
    //             $error->setCarrier('ups');
    //             $error->setCarrierTitle($this->getConfigData('title'));
    //             $error->setErrorMessage($this->getConfigData('specificerrmsg'));
    //         }
    //     }

    //     $result = $this->_rateFactory->create();

    //     if (empty($priceArr)) {
    //         $error = $this->_rateErrorFactory->create();
    //         $error->setCarrier('ups');
    //         $error->setCarrierTitle($this->getConfigData('title'));
    //         if ($this->getConfigData('specificerrmsg') !== '') {
    //             $errorTitle = $this->getConfigData('specificerrmsg');
    //         }
    //         if (!isset($errorTitle)) {
    //             $errorTitle = __('Cannot retrieve shipping rates');
    //         }
    //         $error->setErrorMessage($errorTitle);
    //         $result->append($error);
    //     } else {
    //         foreach ($priceArr as $method => $price) {
    //             $rate = $this->_rateMethodFactory->create();
    //             $rate->setCarrier('ups');
    //             $rate->setCarrierTitle($this->getConfigData('title'));
    //             $rate->setMethod($method);
    //             $methodArr = $this->getShipmentByCode($method);
    //             $rate->setMethodTitle($methodArr);
    //            // $logger->info($etaArr);
    //             //$logger->info($method);
    //             if(isset($etaArr[$method])){
    //                 $rate->setMethodTitle($methodArr .' '.$etaArr[$method]);
    //             }
    //             $rate->setCost($costArr[$method]);
    //             $rate->setPrice($price);
    //             $result->append($rate);
    //         }
    //     }
    //     return $result;
    // }

    /**
     * Processing rate for ship element
     *
     * @param \Magento\Framework\Simplexml\Element $shipElement
     * @param array $allowedMethods
     * @param array $allowedCurrencies
     * @param array $costArr
     * @param array $priceArr
     * @param bool $negotiatedActive
     * @param \Magento\Framework\Simplexml\Config $xml
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    // private function processShippingRateForItem(
    //     \Magento\Framework\Simplexml\Element $shipElement,
    //     array $allowedMethods,
    //     array $allowedCurrencies,
    //     array &$costArr,
    //     array &$priceArr,
    //     bool $negotiatedActive,
    //     \Magento\Framework\Simplexml\Config $xml,
    //     array &$etaArr
    // ): void {
    //     $writer= new \Zend\Log\Writer\Stream(BP . '/var/log/carrier.log');
    //     $logger= new \Zend\Log\Logger();
    //     $logger->addWriter($writer);
    //     //$logger->info('UPS response');
    //     $code = (string)$shipElement->Service->Code;
    //     if (in_array($code, $allowedMethods)) {
    //         //The location of tax information is in a different place
    //         // depending on whether we are using negotiated rates or not
    //         if ($negotiatedActive) {
    //             $includeTaxesArr = $xml->getXpath(
    //                 "//RatingServiceSelectionResponse/RatedShipment/NegotiatedRates"
    //                 . "/NetSummaryCharges/TotalChargesWithTaxes"
    //             );
    //             //$daysInTransit = $shipElement->TimeInTransit->ServiceSummary->EstimatedArrival->TotalTransitDays;
    //             $includeTaxesActive = $this->getConfigFlag('include_taxes') && !empty($includeTaxesArr);
    //             if ($includeTaxesActive) {
    //                 $cost = $shipElement->NegotiatedRates
    //                     ->NetSummaryCharges
    //                     ->TotalChargesWithTaxes
    //                     ->MonetaryValue;

    //                 $responseCurrencyCode = $this->mapCurrencyCode(
    //                     (string)$shipElement->NegotiatedRates
    //                         ->NetSummaryCharges
    //                         ->TotalChargesWithTaxes
    //                         ->CurrencyCode
    //                 );
                
    //             } else {
    //                 $cost = $shipElement->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue;
    //                 $responseCurrencyCode = $this->mapCurrencyCode(
    //                     (string)$shipElement->NegotiatedRates->NetSummaryCharges->GrandTotal->CurrencyCode
    //                 );
    //                 //$logger->info($shipElement->NegotiatedRates->NetSummaryCharges->GrandTotal->CurrencyCode);
                
    //             }
    //         } else {
    //             $includeTaxesArr = $xml->getXpath(
    //                 "//RatingServiceSelectionResponse/RatedShipment/TotalChargesWithTaxes"
    //             );
    //             $includeTaxesActive = $this->getConfigFlag('include_taxes') && !empty($includeTaxesArr);
    //             if ($includeTaxesActive) {
    //                 $cost = $shipElement->TotalChargesWithTaxes->MonetaryValue;
    //                 $responseCurrencyCode = $this->mapCurrencyCode(
    //                     (string)$shipElement->TotalChargesWithTaxes->CurrencyCode
    //                 );
    //                 // $logger->info($shipElement->TotalChargesWithTaxes->CurrencyCode);
    //             } else {
    //                 $cost = $shipElement->TotalCharges->MonetaryValue;
    //                 $responseCurrencyCode = $this->mapCurrencyCode(
    //                     (string)$shipElement->TotalCharges->CurrencyCode
    //                 );
    //             }
    //             $etaDays =$shipElement->GuaranteedDaysToDelivery;
    //             $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    //             $timezoneInterface = $objectManager->create('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
    //             $date = $timezoneInterface->date()->format('Y-m-d');
    //             $etaDate = '';
    //             if(!empty($shipElement->GuaranteedDaysToDelivery)){
    //                 $etaTime = $shipElement->ScheduledDeliveryTime;
    //                // $logger->info($date);
    //                 $etaDate = Date('l, F d', strtotime($date. '+'.$shipElement->GuaranteedDaysToDelivery.' days'));
              
    //             }
    //             //    else {
    //             //     //$logger->info("test -".$date);
    //             //     $etaDate = Date('l, F d', strtotime($date));
    //             //     //$logger->info($etaDate);
    //             // }
    //         }
                   
    //         //convert price with Origin country currency code to base currency code
    //         $successConversion = true;
    //         if ($responseCurrencyCode) {
    //             if (in_array($responseCurrencyCode, $allowedCurrencies)) {
    //                 $cost = (double)$cost * $this->_getBaseCurrencyRate($responseCurrencyCode);
    //             } else {
    //                 $errorTitle = __(
    //                     'We can\'t convert a rate from "%1-%2".',
    //                     $responseCurrencyCode,
    //                     $this->_request->getPackageCurrency()->getCode()
    //                 );
    //                 $error = $this->_rateErrorFactory->create();
    //                 $error->setCarrier('ups');
    //                 $error->setCarrierTitle($this->getConfigData('title'));
    //                 $error->setErrorMessage($errorTitle);
    //                 $successConversion = false;
    //             }
    //         }

    //         if ($successConversion) {
    //             $costArr[$code] = $cost;
    //             $priceArr[$code] = $this->getMethodPrice((float)$cost, $code);
    //              /* ------------ Time In Transit Mod ------------*/
    //             $etaArr[$code] = $etaDate;
    //             /* ----------- End Time In Transit Mod ---------*/
    //         }
    //     }
    // }
    /**
     * Map currency alias to currency code
     *
     * @param string $code
     * @return string
     */
    // private function mapCurrencyCode($code)
    // {
    //     $currencyMapping = [
    //         'RMB' => 'CNY',
    //         'CNH' => 'CNY'
    //     ];

    //     return $currencyMapping[$code] ?? $code;
    // }
}
