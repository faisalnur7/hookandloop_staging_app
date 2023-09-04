<?php
namespace Aheadworks\BlogSearch\Ui\DataProvider\Filter;

use Magento\Framework\Api\Filter;

/**
 * Interface BuilderInterface
 */
interface BuilderInterface
{
    /**
     * Build filter to apply on data provider
     *
     * @return Filter|null
     */
    public function build();
}
