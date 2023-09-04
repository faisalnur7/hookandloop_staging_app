<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\SourceModel;

use Plumrocket\SocialLoginFree\Model\Network\Integration\Twitter\TwitterComposite;

/**
 * @since 4.0.0
 */
class ApiVersion implements \Magento\Framework\Data\OptionSourceInterface
{

    /**
     * Get version list.
     *
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => 0,
                'label' => __('1.1'),
            ],
            [
                'value' => TwitterComposite::OAUTH_V2,
                'label' => __('2'),
            ],
        ];
    }
}
