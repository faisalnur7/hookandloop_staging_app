<?php
/**
 * @package     Plumrocket_SocialLoginFreePro
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network;

use Plumrocket\SocialLoginFree\Api\Data\NetworkAccountInterface;
use Plumrocket\SocialLoginFree\Api\Data\NetworkAccountInterfaceFactory;
use Plumrocket\SocialLoginFree\Model\Network\Data\Converter;

/**
 * @since 3.8.0
 */
class CreateNetworkAccountModel
{

    /**
     * @var \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountInterfaceFactory
     */
    private $networkAccountInterfaceFactory;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\Data\Converter
     */
    private $converter;

    /**
     * @param \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountInterfaceFactory $networkAccountInterfaceFactory
     * @param \Plumrocket\SocialLoginFree\Model\Network\Data\Converter            $converter
     */
    public function __construct(
        NetworkAccountInterfaceFactory $networkAccountInterfaceFactory,
        Converter $converter
    ) {
        $this->networkAccountInterfaceFactory = $networkAccountInterfaceFactory;
        $this->converter = $converter;
    }

    /**
     * Create account model from user data.
     *
     * @param string   $networkCode
     * @param array    $networkAccountData
     * @param array    $fieldsMapping
     * @param string[] $genderMapping
     * @param array    $dobMapping
     * @return \Plumrocket\SocialLoginFree\Api\Data\NetworkAccountInterface
     */
    public function execute(
        string $networkCode,
        array $networkAccountData,
        array $fieldsMapping,
        array $genderMapping = [0 => 'male', 1 => 'female'],
        array $dobMapping = []
    ): NetworkAccountInterface {
        $customerData = $this->converter->convert(
            $networkCode,
            $networkAccountData,
            $fieldsMapping,
            $genderMapping,
            $dobMapping
        );
        return $this->networkAccountInterfaceFactory->create(['data' => $customerData]);
    }
}
