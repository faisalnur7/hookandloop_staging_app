<?php
namespace Aheadworks\Helpdesk2\Model\Source\Department;

use Aheadworks\Helpdesk2\Model\Department\Option\Config;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class StorefrontOptionList
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Department
 */
class StorefrontOptionList implements OptionSourceInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [];
        $groupIndex = 0;

        foreach ($this->config->getAll() as $option) {
            $group = [
                'value' => $groupIndex,
                'label' => __($option['label']),
                'optgroup' => []
            ];

            foreach ($option['types'] as $type) {
                $group['optgroup'][] = ['label' => __($type['label']), 'value' => $type['name']];
            }

            if (count($group['optgroup'])) {
                $options[] = $group;
                $groupIndex ++;
            }
        }

        return $options;
    }
}
