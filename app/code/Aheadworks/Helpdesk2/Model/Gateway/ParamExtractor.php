<?php
namespace Aheadworks\Helpdesk2\Model\Gateway;

use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;

/**
 * Class ParamExtractor
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway
 */
class ParamExtractor
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var array
     */
    private $paramMapper;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param array $paramMapper
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        array $paramMapper = []
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->paramMapper = $paramMapper;
    }

    /**
     * Prepare list of params to make connection
     *
     * @param GatewayDataInterface $gateway
     * @return array
     */
    public function extract($gateway)
    {
        $gatewayData = $this->dataObjectProcessor->buildOutputDataArray(
            $gateway,
            GatewayDataInterface::class
        );

        $params = [];
        foreach ($this->paramMapper as $connectionParam => $gatewayParam) {
            if (isset($gatewayData[$gatewayParam]) && !empty($gatewayData[$gatewayParam])) {
                $params[$connectionParam] = $gatewayData[$gatewayParam];
            } else {
                throw new \InvalidArgumentException(
                    sprintf('Argument: %s - is required', $gatewayParam)
                );
            }
        }

        return $params;
    }
}
