<?php
namespace Aheadworks\Helpdesk2\Model\Source\Product;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class AllowedType
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Product
 */
class AllowedType implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $types;

    /**
     * AllowedType constructor.
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->types;
    }

    /**
     * Get list of allowed products for automation
     *
     * @return array
     */
    public function getTypeList()
    {
        return array_column($this->types, 'value');
    }
}
