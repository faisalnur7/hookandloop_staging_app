<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Data;

use Magento\Eav\Model\Config as EavConfig;

/**
 * @since 4.0.0
 */
class Converter
{

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        EavConfig $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
    }

    /**
     * Convert network account into customer data.
     *
     * @param string $networkCode
     * @param array  $networkAccountData
     * @param array  $fieldsMapping
     * @param array  $genderMapping
     * @param array  $dobMapping
     * @return array
     */
    public function convert(
        string $networkCode,
        array $networkAccountData,
        array $fieldsMapping,
        array $genderMapping = [0 => 'male', 1 => 'female'],
        array $dobMapping = []
    ): array {
        $data = [];
        foreach ($fieldsMapping as $customerField => $networkAccountField) {
            $data[$customerField] = ($networkAccountField && isset($networkAccountData[$networkAccountField]))
                ? $networkAccountData[$networkAccountField]
                : null;
        }

        $data = $this->setDefaultEmptyData($data);
        $data = $this->convertDateOfBirth($data, $dobMapping);
        $data = $this->convertGender($data, $genderMapping);
        $data['network_code'] = $networkCode;
        return $data;
    }

    /**
     * Set default data for customer.
     *
     * @param array $data
     * @return array
     */
    private function setDefaultEmptyData(array $data): array
    {
        $data['email'] = $data['email'] ?? '';
        $data['firstname'] = $data['firstname'] ?? '';
        $data['lastname'] = $data['lastname'] ?? '';
        $data['phone_number'] = $data['phone_number'] ?? '';
        $data['taxvat'] = null;
        return $data;
    }

    /**
     * Fix format of date and set default if needed
     *
     * @param array $data
     * @param array $dob
     * @return array
     */
    private function convertDateOfBirth(array $data, $dob = []): array
    {
        if (! empty($data['dob'])) {
            $data['dob'] = call_user_func_array([$this, 'prepareDob'], array_merge([$data['dob']], $dob));
        } else {
            $data['dob'] = null;
        }
        return $data;
    }

    /**
     * Convert network gender types/labels to magento ids
     *
     * @param array $data
     * @param array $networkGenderMapping
     * @return array
     */
    private function convertGender(array $data, array $networkGenderMapping): array
    {
        if (! empty($data['gender'])) {
            $genderAttribute = $this->eavConfig->getAttribute('customer', 'gender');
            if ($genderAttribute && $options = $genderAttribute->getSource()->getAllOptions(false)) {
                switch ($data['gender']) {
                    case $networkGenderMapping[0]:
                        $data['gender'] = $options[0]['value'];
                        break;
                    case $networkGenderMapping[1]:
                        $data['gender'] = $options[1]['value'];
                        break;
                    default:
                        $data['gender'] = null;
                }
            } else {
                $data['gender'] = null;
            }
        } else {
            $data['gender'] = null;
        }
        return $data;
    }

    /**
     * Convert date.
     *
     * @param $date
     * @param $p1
     * @param $p2
     * @param $p3
     * @param $separator
     * @return string
     */
    private function prepareDob($date, $p1 = 'month', $p2 = 'day', $p3 = 'year', $separator = '/'): string
    {
        $date = explode($separator, $date);

        $result = [
            'year' => '0000',
            'month' => '00',
            'day' => '00'
        ];

        $result[$p1] = $date[0];
        if (isset($date[1])) {
            $result[$p2] = $date[1];
        }
        if (isset($date[2])) {
            $result[$p3] = $date[2];
        }

        return implode('-', array_values($result));
    }
}
