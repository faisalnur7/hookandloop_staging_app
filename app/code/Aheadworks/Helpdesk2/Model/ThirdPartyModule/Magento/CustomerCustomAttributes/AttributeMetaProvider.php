<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\ThirdPartyModule\Magento\CustomerCustomAttributes;

use Aheadworks\Helpdesk2\Model\ThirdPartyModule\CustomerAttribute\AbstractAttributeMetaProvider;

/**
 * Class AttributeMetaProvider
 */
class AttributeMetaProvider extends AbstractAttributeMetaProvider
{
    /**
     * Customer attribute form code
     */
    public const FORM_CODE = 'aw_helpdesk2_admin_ticket_view';

    /**
     * Get form code to get attributes
     *
     * @return string
     */
    protected function getFormCode(): string
    {
        return self::FORM_CODE;
    }

    /**
     * @return string
     */
    protected function getRoutePath(): string
    {
        return 'aw_helpdesk2/thirdparty_magento_customercustomattributes/save';
    }
}
