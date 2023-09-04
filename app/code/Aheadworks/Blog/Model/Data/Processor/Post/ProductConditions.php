<?php
namespace Aheadworks\Blog\Model\Data\Processor\Post;

use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface;
use Aheadworks\Blog\Model\Converter\Condition as ConditionConverter;
use Aheadworks\Blog\Model\Serialize\SerializeInterface;
use Aheadworks\Blog\Model\Rule\ProductFactory;
use Aheadworks\Blog\Model\Serialize\Factory as SerializeFactory;
use Aheadworks\Blog\Api\Data\PostInterface;

/**
 * Class ProductConditions
 * @package Aheadworks\Blog\Model\Data\Processor\Post
 */
class ProductConditions implements ProcessorInterface
{
    /**
     * @var SerializeInterface
     */
    private $serializer;

    /**
     * @var ProductFactory
     */
    private $productRuleFactory;

    /**
     * @var ConditionConverter
     */
    private $conditionConverter;

    /**
     * @param ConditionConverter $conditionConverter
     * @param ProductFactory $productRuleFactory
     * @param SerializeFactory $serializeFactory
     */
    public function __construct(
        ConditionConverter $conditionConverter,
        ProductFactory $productRuleFactory,
        SerializeFactory $serializeFactory
    ) {
        $this->conditionConverter = $conditionConverter;
        $this->productRuleFactory = $productRuleFactory;
        $this->serializer = $serializeFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        $arrayForConvertation = [];

        if (isset($data['rule']['conditions'])) {
            $conditionArray = $this->convertFlatToRecursive($data['rule'], ['conditions']);
            if (is_array($conditionArray['conditions']['1'])) {
                $arrayForConvertation = $conditionArray['conditions']['1'];
            }
        } elseif (isset($data[PostInterface::PRODUCT_CONDITION]) && !empty($data[PostInterface::PRODUCT_CONDITION])) {
            $arrayForConvertation = $this->serializer->unserialize($data[PostInterface::PRODUCT_CONDITION]);
        } else {
            $productRule = $this->productRuleFactory->create();
            $arrayForConvertation = $productRule->setConditions([])->getConditions()->asArray();
        }
        $data[PostInterface::PRODUCT_CONDITION] = $this->conditionConverter
            ->arrayToDataModel($arrayForConvertation);

        return $data;
    }

    /**
     * Get conditions data recursively
     *
     * @param array $data
     * @param array $allowedKeys
     * @return array
     */
    private function convertFlatToRecursive(array $data, $allowedKeys = [])
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedKeys) && is_array($value)) {
                foreach ($value as $id => $data) {
                    $path = explode('--', (string)$id);
                    $node = & $result;

                    for ($i = 0, $l = sizeof($path); $i < $l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = [];
                        }
                        $node = & $node[$key][$path[$i]];
                    }
                    foreach ($data as $k => $v) {
                        $node[$k] = $v;
                    }
                }
            }
        }
        return $result;
    }
}
