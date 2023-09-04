<?php

namespace Exinent\DimweightFedex\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;
class Carrier extends \Magento\Fedex\Model\Carrier {

    public function collectRates(RateRequest $request) {

        if (!$this->getConfigFlag($this->_activeFlag)) {
            return false;
        }

        if ($this->_scopeConfig->getValue('dimweightfedex/general_settings/enable')) {
            $request->setUseDimweightOverride(true); // enable for fedex
        }
        $result = parent::collectRates($request);
        if ($this->_scopeConfig->getValue('dimweightfedex/general_settings/enable')) {
            $request->setUseDimweightOverride(false); // reset so as to not interfere with other carriers
        }

        return $result;
    }
    
    /**
     * Get xml quotes
     *
     * @return Result
     */
    protected function _getXmlQuotes()
    {
        $r = $this->_rawRequest;
        $xml = $this->_xmlElFactory->create(
            ['data' => '<?xml version = "1.0" encoding = "UTF-8"?><FDXRateAvailableServicesRequest/>']
        );

        $xml->addAttribute('xmlns:api', 'http://www.fedex.com/fsmapi');
        $xml->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $xml->addAttribute('xsi:noNamespaceSchemaLocation', 'FDXRateAvailableServicesRequest.xsd');

        $requestHeader = $xml->addChild('RequestHeader');
        $requestHeader->addChild('AccountNumber', $r->getAccount());
        $requestHeader->addChild('MeterNumber', '0');

        $xml->addChild('ShipDate', date('Y-m-d'));
        $xml->addChild('DropoffType', $r->getDropoffType());
        if ($r->hasService()) {
            $xml->addChild('Service', $r->getService());
        }
        $xml->addChild('Packaging', $r->getPackaging());
        $xml->addChild('WeightUnits', 'LBS');
        $xml->addChild('Weight', $r->getWeight());

        $originAddress = $xml->addChild('OriginAddress');
        $originAddress->addChild('PostalCode', $r->getOrigPostal());
        $originAddress->addChild('CountryCode', $r->getOrigCountry());

        $destinationAddress = $xml->addChild('DestinationAddress');
        $destinationAddress->addChild('PostalCode', $r->getDestPostal());
        $destinationAddress->addChild('CountryCode', $r->getDestCountry());

        $payment = $xml->addChild('Payment');
        $payment->addChild('PayorType', 'SENDER');

        $declaredValue = $xml->addChild('DeclaredValue');
        $declaredValue->addChild('Value', $r->getValue());
        $declaredValue->addChild('CurrencyCode', $this->getCurrencyCode());

        if ($this->getConfigData('residence_delivery')) {
            $specialServices = $xml->addChild('SpecialServices');
            $specialServices->addChild('ResidentialDelivery', 'true');
        }

        $xml->addChild('PackageCount', '1');

        $request = $xml->asXML();

        $responseBody = $this->_getCachedQuotes($request);
        if ($responseBody === null) {
            $debugData = ['request' => parent::filterDebugData($request)];
            try {
                $url = $this->getConfigData('gateway_url');
                if (!$url) {
                    $url = $this->_defaultGatewayUrl;
                }
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
                $responseBody = curl_exec($ch);
                curl_close($ch);

                $debugData['result'] = parent::filterDebugData($responseBody);
                $this->_setCachedQuotes($request, $responseBody);
            } catch (\Exception $e) {
                $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
                $responseBody = '';
            }
            $this->_debug($debugData);
        }

        return $this->_parseXmlResponse($responseBody);
    }
    
     /**
     * Get configuration data of carrier
     *
     * @param string $type
     * @param string $code
     * @return array|false
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getCode($type, $code = '')
    {
        $codes = [
            'method' => [
                'EUROPE_FIRST_INTERNATIONAL_PRIORITY' => __('Europe First Priority'),
                'FEDEX_1_DAY_FREIGHT' => __('1 Day Freight'),
                'FEDEX_2_DAY_FREIGHT' => __('2 Day Freight'),
                'FEDEX_2_DAY' => __('2 Day'),
                'FEDEX_2_DAY_AM' => __('2 Day AM'),
                'FEDEX_3_DAY_FREIGHT' => __('3 Day Freight'),
                'FEDEX_EXPRESS_SAVER' => __('Express Saver'),
                'FEDEX_GROUND' => __('Ground'),
                'FIRST_OVERNIGHT' => __('First Overnight'),
                'GROUND_HOME_DELIVERY' => __('Ground'),
                'INTERNATIONAL_ECONOMY' => __('International Economy'),
                'INTERNATIONAL_ECONOMY_FREIGHT' => __('Intl Economy Freight'),
                'INTERNATIONAL_FIRST' => __('International First'),
                'INTERNATIONAL_GROUND' => __('International Ground'),
                'INTERNATIONAL_PRIORITY' => __('International Priority'),
                'INTERNATIONAL_PRIORITY_FREIGHT' => __('Intl Priority Freight'),
                'PRIORITY_OVERNIGHT' => __('Priority Overnight'),
                'SMART_POST' => __('Smart Post'),
                'STANDARD_OVERNIGHT' => __('Standard Overnight'),
                'FEDEX_FREIGHT' => __('Freight'),
                'FEDEX_NATIONAL_FREIGHT' => __('National Freight'),
            ],
            'dropoff' => [
                'REGULAR_PICKUP' => __('Regular Pickup'),
                'REQUEST_COURIER' => __('Request Courier'),
                'DROP_BOX' => __('Drop Box'),
                'BUSINESS_SERVICE_CENTER' => __('Business Service Center'),
                'STATION' => __('Station'),
            ],
            'packaging' => [
                'FEDEX_ENVELOPE' => __('FedEx Envelope'),
                'FEDEX_PAK' => __('FedEx Pak'),
                'FEDEX_BOX' => __('FedEx Box'),
                'FEDEX_TUBE' => __('FedEx Tube'),
                'FEDEX_10KG_BOX' => __('FedEx 10kg Box'),
                'FEDEX_25KG_BOX' => __('FedEx 25kg Box'),
                'YOUR_PACKAGING' => __('Your Packaging'),
            ],
            'containers_filter' => [
                [
                    'containers' => ['FEDEX_ENVELOPE', 'FEDEX_PAK'],
                    'filters' => [
                        'within_us' => [
                            'method' => [
                                'FEDEX_EXPRESS_SAVER',
                                'FEDEX_2_DAY',
                                'FEDEX_2_DAY_AM',
                                'STANDARD_OVERNIGHT',
                                'PRIORITY_OVERNIGHT',
                                'FIRST_OVERNIGHT',
                            ],
                        ],
                        'from_us' => [
                            'method' => ['INTERNATIONAL_FIRST', 'INTERNATIONAL_ECONOMY', 'INTERNATIONAL_PRIORITY'],
                        ],
                    ],
                ],
                [
                    'containers' => ['FEDEX_BOX', 'FEDEX_TUBE'],
                    'filters' => [
                        'within_us' => [
                            'method' => [
                                'FEDEX_2_DAY',
                                'FEDEX_2_DAY_AM',
                                'STANDARD_OVERNIGHT',
                                'PRIORITY_OVERNIGHT',
                                'FIRST_OVERNIGHT',
                                'FEDEX_FREIGHT',
                                'FEDEX_1_DAY_FREIGHT',
                                'FEDEX_2_DAY_FREIGHT',
                                'FEDEX_3_DAY_FREIGHT',
                                'FEDEX_NATIONAL_FREIGHT',
                            ],
                        ],
                        'from_us' => [
                            'method' => ['INTERNATIONAL_FIRST', 'INTERNATIONAL_ECONOMY', 'INTERNATIONAL_PRIORITY'],
                        ],
                    ],
                ],
                [
                    'containers' => ['FEDEX_10KG_BOX', 'FEDEX_25KG_BOX'],
                    'filters' => [
                        'within_us' => [],
                        'from_us' => ['method' => ['INTERNATIONAL_PRIORITY']],
                    ],
                ],
                [
                    'containers' => ['YOUR_PACKAGING'],
                    'filters' => [
                        'within_us' => [
                            'method' => [
                                'FEDEX_GROUND',
                                'GROUND_HOME_DELIVERY',
                                'SMART_POST',
                                'FEDEX_EXPRESS_SAVER',
                                'FEDEX_2_DAY',
                                'FEDEX_2_DAY_AM',
                                'STANDARD_OVERNIGHT',
                                'PRIORITY_OVERNIGHT',
                                'FIRST_OVERNIGHT',
                                'FEDEX_FREIGHT',
                                'FEDEX_1_DAY_FREIGHT',
                                'FEDEX_2_DAY_FREIGHT',
                                'FEDEX_3_DAY_FREIGHT',
                                'FEDEX_NATIONAL_FREIGHT',
                            ],
                        ],
                        'from_us' => [
                            'method' => [
                                'INTERNATIONAL_FIRST',
                                'INTERNATIONAL_ECONOMY',
                                'INTERNATIONAL_PRIORITY',
                                'INTERNATIONAL_GROUND',
                                'FEDEX_FREIGHT',
                                'FEDEX_1_DAY_FREIGHT',
                                'FEDEX_2_DAY_FREIGHT',
                                'FEDEX_3_DAY_FREIGHT',
                                'FEDEX_NATIONAL_FREIGHT',
                                'INTERNATIONAL_ECONOMY_FREIGHT',
                                'INTERNATIONAL_PRIORITY_FREIGHT',
                            ],
                        ],
                    ],
                ],
            ],
            'delivery_confirmation_types' => [
                'NO_SIGNATURE_REQUIRED' => __('Not Required'),
                'ADULT' => __('Adult'),
                'DIRECT' => __('Direct'),
                'INDIRECT' => __('Indirect'),
            ],
            'unit_of_measure' => [
                'LB' => __('Pounds'),
                'KG' => __('Kilograms'),
            ],
        ];

        if (!isset($codes[$type])) {
            return false;
        } elseif ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }

    public function formatEstimatedDeliveryDate($deliveryTimeStamp)
    {
        return __(date('l, F d', strtotime($deliveryTimeStamp)));
    }

     private function calculatePickupDate()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create('\Exinent\DimweightFedex\Helper\Data');
        $currentTimeStamp = $helper->_timezoneInterface->date()->format('c');
        $currentTimeStamp = date('c', strtotime($currentTimeStamp . ' +1 Weekday'));

        return $currentTimeStamp;
    }
    /**
     * Prepare shipping rate result based on response
     *
     * @param mixed $response
     * @return Result
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _prepareRateResponse($response)
    {
        $costArr = [];
        $priceArr = [];
        $deliveryDateArr = [];
        $checkoutSessionFedexArr=[];
        $errorTitle = 'For some reason we can\'t retrieve tracking info right now.';
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $checkoutSessionFedex = $objectManager->create('\Magento\Checkout\Model\Session');
        if (is_object($response)) {
            if ($response->HighestSeverity == 'FAILURE' || $response->HighestSeverity == 'ERROR') {
                if (is_array($response->Notifications)) {
                    $notification = array_pop($response->Notifications);
                    $errorTitle = (string)$notification->Message;
                } else {
                    $errorTitle = (string)$response->Notifications->Message;
                }
            } elseif (isset($response->RateReplyDetails)) {
                $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));

                if (is_array($response->RateReplyDetails)) {
                    foreach ($response->RateReplyDetails as $rate) {
                        $serviceName = (string)$rate->ServiceType;
                        if (in_array($serviceName, $allowedMethods)) {
                            $amount = $this->_getRateAmountOriginBased($rate);
                            $costArr[$serviceName] = $amount;
                            $priceArr[$serviceName] = $this->getMethodPrice($amount, $serviceName);
                            //$r=$rate->$deliveryTimeStamp
                            date('l, M d', strtotime($rate->DeliveryTimestamp));
                            if (!empty($rate->DeliveryTimestamp)) {
                                 $checkoutSessionFedexArr['fedex_'.$serviceName]=date('l, M d', strtotime($rate->DeliveryTimestamp));
                                $checkoutSessionFedex->setEtaCheckoutFedex($checkoutSessionFedexArr);
                                $deliveryDateArr[$serviceName] = $this->formatEstimatedDeliveryDate($rate->DeliveryTimestamp);
                            }
                        }
                    }
                    asort($priceArr);
                } else {
                    $rate = $response->RateReplyDetails;
                    $serviceName = (string)$rate->ServiceType;
                    if (in_array($serviceName, $allowedMethods)) {
                        $amount = $this->_getRateAmountOriginBased($rate);
                        $costArr[$serviceName] = $amount;
                        $priceArr[$serviceName] = $this->getMethodPrice($amount, $serviceName);
                    }
                }
            }
        }

        $result = $this->_rateFactory->create();
        if (empty($priceArr)) {
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($errorTitle);
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        } else {
            foreach ($priceArr as $method => $price) {
                $rate = $this->_rateMethodFactory->create();
                $rate->setCarrier($this->_code);
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                $rate->setDeliveryDate($deliveryDateArr[$method]);
                $rate->setEtaDeliveryTime($deliveryDateArr[$method]);
                $rate->setMethodTitle($this->getCode('method', $method));
                $rate->setCost($costArr[$method]);
                $rate->setPrice($price);              
                $result->append($rate);
            }
        }

        return $result;
    }
    /**
     * Forming request for rate estimation depending to the purpose
     *
     * @param string $purpose
     * @return array
     */
    protected function _formRateRequest($purpose)
    {
       
        $r = $this->_rawRequest;
       
        $ratesRequest = [
            'WebAuthenticationDetail' => [
                'UserCredential' => ['Key' => $r->getKey(), 'Password' => $r->getPassword()],
            ],
            'ClientDetail' => ['AccountNumber' => $r->getAccount(), 'MeterNumber' => $r->getMeterNumber()],
            'Version' => $this->getVersionInfo(),
            'RequestedShipment' => [
                'DropoffType' => $r->getDropoffType(),
                'ShipTimestamp' => $this->calculatePickupDate(),
                'PackagingType' => $r->getPackaging(),
                'Shipper' => [
                    'Address' => ['PostalCode' => $r->getOrigPostal(), 'CountryCode' => $r->getOrigCountry()],
                ],
                'Recipient' => [
                    'Address' => [
                        'PostalCode' => $r->getDestPostal(),
                        'CountryCode' => $r->getDestCountry(),
                        'StateOrProvinceCode' => $r->getDestRegionCode(),
                        'Residential' => (bool)$this->getConfigData('residence_delivery'),
                    ],
                ],
                'ShippingChargesPayment' => [
                    'PaymentType' => 'SENDER',
                    'Payor' => ['AccountNumber' => $r->getAccount(), 'CountryCode' => $r->getOrigCountry()],
                ],
                'CustomsClearanceDetail' => [
                    'CustomsValue' => ['Amount' => $r->getValue(), 'Currency' => $this->getCurrencyCode()],
                ],
                'RateRequestTypes' => 'LIST',
                'PackageCount' => '1',
                'PackageDetail' => 'INDIVIDUAL_PACKAGES',
                'RequestedPackageLineItems' => [
                    '0' => [
                        'Weight' => [
                            'Value' => (double)$r->getWeight(),
                            'Units' => $this->getConfigData('unit_of_measure'),
                        ],
                        'GroupPackageCount' => 1,
                    ],
                ],
            ],
        ];

        if ($r->getDestCity()) {
            $ratesRequest['RequestedShipment']['Recipient']['Address']['City'] = $r->getDestCity();
        }

        if ($purpose != self::RATE_REQUEST_GENERAL && $purpose == self::RATE_REQUEST_SMARTPOST) {
            $ratesRequest['RequestedShipment']['ServiceType'] = self::RATE_REQUEST_SMARTPOST;
            $ratesRequest['RequestedShipment']['SmartPostDetail'] = [
                'Indicia' => (double)$r->getWeight() >= 1 ? 'PARCEL_SELECT' : 'PRESORTED_STANDARD',
                'HubId' => $this->getConfigData('smartpost_hubid'),
            ];
        }

        //Request Estimated Delivery Date
        $ratesRequest['ReturnTransitAndCommit'] = true;
        return $ratesRequest;
    }
}
