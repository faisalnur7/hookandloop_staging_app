<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Plugin\AdvancedReviewAndReminder\Model\Provider;

use Plumrocket\AdvancedReviewAndReminder\Model\Provider\SocialButtons;
use Plumrocket\SocialLoginFree\Api\Data\ButtonInterface;
use Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface;
use Plumrocket\SocialLoginFree\Helper\Config;

class SocialButtonsPlugin
{

    /**
     * @var \Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface
     */
    private $buttonProvider;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @param \Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface $buttonProvider
     * @param \Plumrocket\SocialLoginFree\Helper\Config                      $config
     */
    public function __construct(
        NetworkButtonProviderInterface $buttonProvider,
        Config $config
    ) {
        $this->buttonProvider = $buttonProvider;
        $this->config = $config;
    }

    /**
     * Add social login buttons to advanced reviews.
     *
     * @param \Plumrocket\AdvancedReviewAndReminder\Model\Provider\SocialButtons $subject
     * @param array                                                              $result
     * @return array
     */
    public function afterGetList( //@codingStandardsIgnoreLine
        SocialButtons $subject,
        array $result
    ): array {
        if (! $this->config->isModuleEnabled()) {
            return $result;
        }

        $socialButtonsList = array_map(static function (ButtonInterface $button) {
            return $button->toArray();
        }, $this->buttonProvider->getDefaultList());
        return array_merge($result, $socialButtonsList);
    }
}
