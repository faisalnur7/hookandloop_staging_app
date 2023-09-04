<?php

namespace Exinent\DimweightFedex\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper 
{       
        protected $scopeConfig;
        public $_timezoneInterface;
        
	const THRESHOLD_IN = 5184;
	const THRESHOLD_CM = 84951;

	const DIVISOR_IN = 166;
	const DIVISOR_CM = 6000;
        
        public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface)
        {
            $this->scopeConfig = $scopeConfig;
            $this->_timezoneInterface = $timezoneInterface;
        }

	public function getPackageAttribute( $dimension ){
		return $this->scopeConfig->getValue('dimweightfedex/general_settings/'. $dimension .'_attribute');
	}

	public function getDimweight( $package_length, $package_width, $package_height ){
		if( ( $package_length * $package_width * $package_height ) > $this->getThreshold( $this->scopeConfig->getValue('dimweightfedex/general_settings/units') ) ){
			return ( ceil( ( $package_length * $package_width * $package_height ) / $this->getDivisor( $this->scopeConfig->getValue('dimweightfedex/general_settings/units') ) ) );
		}
		return 0;
	}

	public function getThreshold( $units ){
		if( $units == 'cm' ){
			return self::THRESHOLD_CM;
		}
		return self::THRESHOLD_IN;
	}

	public function getDivisor( $units ){
		if( $units == 'cm' ){
			return self::DIVISOR_CM;
		}
		return self::DIVISOR_IN;
	}

	public function getTimezone(){
		return $this->_timezoneInterface;
	}

}