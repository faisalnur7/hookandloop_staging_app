<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Template;

/**
 * Class Filter
 */
class Filter extends \Magento\Email\Model\Template\Filter
{
    /**
     * Process {{for}} directive regex match
     *
     * @param string[] $construction
     * @return string
     */
    public function forDirective($construction)
    {
        return $construction[0];
    }
}
