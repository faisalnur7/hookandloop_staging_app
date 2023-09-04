<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Plugin\ThirdParty\CustomerCustomAttributes\Helper;

use Aheadworks\Helpdesk2\Model\ThirdPartyModule\Magento\CustomerCustomAttributes\AttributeMetaProvider;
use Magento\CustomerCustomAttributes\Helper\Customer as Subject;

/**
 * Class CustomerPlugin
 */
class CustomerPlugin
{
    /**
     * Return available customer attribute form as select options
     *
     * @param Subject $subject
     * @param array $result
     * @return array
     */
    public function afterGetAttributeFormOptions(Subject $subject, array $result): array
    {
        $result[] = [
            'label' => __('Show in helpdesk ticket'),
            'value' => AttributeMetaProvider::FORM_CODE
        ];
        return $result;
    }
}
