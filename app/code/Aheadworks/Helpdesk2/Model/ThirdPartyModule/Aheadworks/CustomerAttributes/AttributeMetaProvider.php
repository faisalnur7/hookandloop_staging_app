<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\ThirdPartyModule\Aheadworks\CustomerAttributes;

use Aheadworks\Helpdesk2\Model\ThirdPartyModule\CustomerAttribute\AbstractAttributeMetaProvider;
use Aheadworks\Helpdesk2\Plugin\ThirdParty\AwCustomerAttributes\UsedInFormsPlugin;

/**
 * Class AttributeMetaProvider
 *
 * @package Aheadworks\Helpdesk2\Model\ThirdPartyModule\Aheadworks\CustomerAttributes
 */
class AttributeMetaProvider extends AbstractAttributeMetaProvider
{
    /**
     * Get form code to get attributes
     *
     * @return string
     */
    protected function getFormCode(): string
    {
        return UsedInFormsPlugin::ADMIN_TICKET_VIEW;
    }

    /**
     * Get route path to update attribute data
     *
     * @return string
     */
    protected function getRoutePath(): string
    {
        return 'aw_helpdesk2/thirdparty_aheadworks_customerattributes/save';
    }
}
