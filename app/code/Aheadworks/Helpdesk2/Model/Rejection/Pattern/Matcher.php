<?php
namespace Aheadworks\Helpdesk2\Model\Rejection\Pattern;

use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Magento\Framework\DataObject;

/**
 * Class Matcher
 *
 * @package Aheadworks\Helpdesk2\Model\Rejection\Pattern
 */
class Matcher
{
    /**
     * Check if message is matching pattern
     *
     * @param RejectingPatternInterface $pattern
     * @param DataObject $data
     * @return bool
     */
    public function isMatching($pattern, $data)
    {
        foreach ($pattern->getScopeTypes() as $type) {
            if ($data->hasData($type)) {
                if (preg_match($pattern->getPattern(), (string)$data->getData($type))) {
                    return true;
                }
            }
        }

        return false;
    }
}
