<?php
namespace Aheadworks\Blog\Model\Export\Collection\Filter;

use Aheadworks\Blog\Model\ResourceModel\AbstractCollection;

/**
 * Class IntFilter
 */
class IntFilter implements ProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function process(AbstractCollection $collection, $columnName, $value, $type = null)
    {
        if (is_array($value)) {
            $from = $value[0] ?? null;
            $to = $value[1] ?? null;

            if (is_numeric($from) && !empty($from)) {
                $collection->addFieldToFilter($columnName, ['from' => $from]);
            }

            if (is_numeric($to) && !empty($to)) {
                $collection->addFieldToFilter($columnName, ['to' => $to]);
            }

            return;
        }

        $collection->addFieldToFilter($columnName, ['eq' => $value]);
    }
}