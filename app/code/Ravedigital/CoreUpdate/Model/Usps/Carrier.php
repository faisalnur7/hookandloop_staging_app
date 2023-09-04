<?php
namespace Ravedigital\CoreUpdate\Model\Usps;

class Carrier extends \Magento\Usps\Model\Carrier
{
    private static $weightPrecision = 10;
    

    public function getAllItems(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        $items = [];
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                /* @var $item \Magento\Quote\Model\Quote\Item */
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    // Don't process children here - we will process (or already have processed) them below
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if (!$child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $items[] = $child;
                        }
                    }
                } else {
                    // Ship together - count compound item as one solid
                    $items[] = $item;
                }
            }
        }

        return $items;
    }

    public function setRequest(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        $this->_request = $request;
        $fetchItemsWeight= 0;
        $r = new \Magento\Framework\DataObject();
        $maxAllowedWeight = (double)$this->getConfigData('max_package_weight');
        foreach ($this->getAllItems($this->_request) as $item) {
            $product = $item->getProduct();
            if ($product && $product->getId()) {
                $weight = $product->getWeight();
                $stockItemData = $this->stockRegistry->getStockItem(
                    $product->getId(),
                    $item->getStore()->getWebsiteId()
                );
                $doValidation = true;
                if ($stockItemData->getIsQtyDecimal() && $stockItemData->getIsDecimalDivided()) {
                    if ($stockItemData->getEnableQtyIncrements() && $stockItemData->getQtyIncrements()
                    ) {
                        $weight += $weight * $stockItemData->getQtyIncrements();
                    } else {
                        $doValidation = false;
                    }
                } elseif ($stockItemData->getIsQtyDecimal() && !$stockItemData->getIsDecimalDivided()) {
                    $weight += $weight * $item->getQty();
                } else {
                    $weight += $weight * $item->getQty();
                }
                
            }
            $fetchItemsWeight += $weight;
        }
       
        if ($request->getLimitMethod()) {
            $r->setService($request->getLimitMethod());
        } else {
            $r->setService('ALL');
        }

        if ($request->getUspsUserid()) {
            $userId = $request->getUspsUserid();
        } else {
            $userId = $this->getConfigData('userid');
        }
        $r->setUserId($userId);

        if ($request->getUspsContainer()) {
            $container = $request->getUspsContainer();
        } else {
            $container = $this->getConfigData('container');
        }
        $r->setContainer($container);

        if ($request->getUspsSize()) {
            $size = $request->getUspsSize();
        } else {
            $size = $this->getConfigData('size');
        }
        $r->setSize($size);

        if ($request->getGirth()) {
            $girth = $request->getGirth();
        } else {
            $girth = $this->getConfigData('girth');
        }
        $r->setGirth($girth);

        if ($request->getHeight()) {
            $height = $request->getHeight();
        } else {
            $height = $this->getConfigData('height');
        }
        $r->setHeight($height);

        if ($request->getLength()) {
            $length = $request->getLength();
        } else {
            $length = $this->getConfigData('length');
        }
        $r->setLength($length);

        if ($request->getWidth()) {
            $width = $request->getWidth();
        } else {
            $width = $this->getConfigData('width');
        }
        $r->setWidth($width);

        if ($request->getUspsMachinable()) {
            $machinable = $request->getUspsMachinable();
        } else {
            $machinable = $this->getConfigData('machinable');
        }
        $r->setMachinable($machinable);

        if ($request->getOrigPostcode()) {
            $r->setOrigPostal($request->getOrigPostcode());
        } else {
            $r->setOrigPostal(
                $this->_scopeConfig->getValue(
                    \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ZIP,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStoreId()
                )
            );
        }

        if ($request->getOrigCountryId()) {
            $r->setOrigCountryId($request->getOrigCountryId());
        } else {
            $r->setOrigCountryId(
                $this->_scopeConfig->getValue(
                    \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_COUNTRY_ID,
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

        $r->setDestCountryId($destCountry);

        if (!$this->_isUSCountry($destCountry)) {
            $r->setDestCountryName($this->_getCountryName($destCountry));
        }

        if ($request->getDestPostcode()) {
            $r->setDestPostal($request->getDestPostcode());
        }

        if ($request->getPackageWeight() != 0) {
            $weight = $this->getTotalNumOfBoxes($request->getPackageWeight());
        }

        $weight = $fetchItemsWeight;

        $r->setWeightPounds(floor($weight));
        $ounces = ($weight - floor($weight)) * self::OUNCES_POUND;
        $r->setWeightOunces(sprintf('%.' . self::$weightPrecision . 'f', $ounces));
        if ($request->getFreeMethodWeight() != $request->getPackageWeight()) {
            $r->setFreeMethodWeight($request->getFreeMethodWeight());
        }

        $r->setPackages($this->createPackages((float) $request->getPackageWeight(), (array) $request->getPackages()));
        $r->setWeightPounds(floor($request->getPackageWeight()));
        $ounces = ($request->getPackageWeight() - floor($request->getPackageWeight())) * self::OUNCES_POUND;
        $r->setWeightOunces(sprintf('%.' . self::$weightPrecision . 'f', $ounces));
        $r->setValue($request->getPackageValue());
        $r->setValueWithDiscount($request->getPackageValueWithDiscount());

        $r->setBaseSubtotalInclTax($request->getBaseSubtotalInclTax());

        $this->setRawRequest($r);
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
                $packages[$i]['weight_pounds'] = floor($dividedWeight);
                $ounces = ($dividedWeight - floor($dividedWeight)) * self::OUNCES_POUND;
                $packages[$i]['weight_ounces'] = sprintf('%.' . self::$weightPrecision . 'f', $ounces);
            }
        } else {
            foreach ($packages as $key => $package) {
                $packages[$key]['weight_pounds'] = floor($package['weight']);
                $ounces = ($package['weight'] - floor($package['weight'])) * self::OUNCES_POUND;
                $packages[$key]['weight_ounces'] = sprintf('%.' . self::$weightPrecision . 'f', $ounces);
            }
        }
        $this->_numBoxes = count($packages);

        return $packages;
    }

    protected function _parseXmlResponse($response)
    {
        $writer= new \Zend\Log\Writer\Stream(BP . '/var/log/carrier.log');
        $logger= new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $r = $this->_rawRequest;
        $allowedMethods = explode(',', $this->getConfigData('allowed_methods') ?? '');
        $serviceCodeToActualNameMap = [];
        $isUS = $this->_isUSCountry($r->getDestCountryId());
        $costArr = [];
        $logger->info('USPS response start');
        if (strlen(trim($response)) > 0 && strpos(trim($response), '<?xml') === 0) {
            if (strpos($response, '<?xml version="1.0"?>') !== false) {
                $response = str_replace(
                    '<?xml version="1.0"?>',
                    '<?xml version="1.0" encoding="ISO-8859-1"?>',
                    $response
                );
            }
            $xml = $this->parseXml($response);
            if (is_object($xml) && is_object($xml->Package)) {
                foreach ($xml->Package as $package) {
                    if ($isUS && is_object($package->Postage)) {
                        /**
                         * US Rates
                         */
                        foreach ($package->Postage as $postage) {
                             $logger->info($postage);
                            $serviceName = $this->_filterServiceName((string)$postage->MailService);
                            $serviceCode = $this->getCode('method_to_code', $serviceName)
                                ?: (string) $postage->attributes()->CLASSID;

                            $serviceCodeToActualNameMap[$serviceCode] = $serviceName;
                            if (in_array($serviceCode, $allowedMethods)) {
                                $costArr[$serviceCode] = isset($costArr[$serviceCode])
                                    ? $costArr[$serviceCode] + (float) $postage->Rate
                                    : (float) $postage->Rate;
                            }
                        }
                    } elseif (!$isUS && is_object($package->Service)) {
                        /*
                         * International Rates
                         */
                        foreach ($package->Service as $service) {
                            $serviceName = $this->_filterServiceName((string)$service->SvcDescription);
                            $serviceCode = 'INT_' . $service->attributes()->ID;

                            $serviceCodeToActualNameMap[$serviceCode] = $serviceName;
                            if ($this->isServiceAvailable($service) && in_array($serviceCode, $allowedMethods)) {
                                $costArr[$serviceCode] = isset($costArr[$serviceCode])
                                    ? $costArr[$serviceCode] + (float) $service->Postage
                                    : (float) $service->Postage;
                            }
                        }
                    }
                }
            }
        }
        uasort($costArr, function ($previous, $next) {
            return ($previous <= $next) ? -1 : 1;
        });
        $result = $this->_rateFactory->create();
        if (empty($costArr)) {
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier('usps');
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        } else {
            foreach ($costArr as $method => $cost) {
                $rate = $this->_rateMethodFactory->create();
                $rate->setCarrier('usps');
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                $rate->setMethodTitle($serviceCodeToActualNameMap[$method] ?? $this->getCode('method', $method));
                $rate->setCost($costArr[$method]);
                $rate->setPrice($this->getMethodPrice($cost, $method));
                $result->append($rate);
            }
        }

        return $result;
    }

    /**
     * Check availability of post service
     *
     * @param \SimpleXMLElement $service
     * @return boolean
     */
    private function isServiceAvailable(\SimpleXMLElement $service)
    {
        // Allow services which which don't provide any ExtraServices
        if (empty($service->ExtraServices->children()->count())) {
            return true;
        }

        foreach ($service->ExtraServices->children() as $child) {
            if (filter_var($child->Available, FILTER_VALIDATE_BOOLEAN)) {
                return true;
            }
        }
        return false;
    }
}
