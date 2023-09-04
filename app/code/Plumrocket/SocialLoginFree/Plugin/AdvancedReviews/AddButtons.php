<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2023 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Plugin\AdvancedReviews;

use Plumrocket\AdvancedReviewAndReminder\Block\Form;
use Plumrocket\SocialLoginFree\Helper\JsButtonProvider;

/**
 * @since 4.2.0
 */
class AddButtons
{

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\JsButtonProvider
     */
    private $jsButtonProvider;

    /**
     * @param \Plumrocket\SocialLoginFree\Helper\JsButtonProvider $jsButtonProvider
     */
    public function __construct(JsButtonProvider $jsButtonProvider)
    {
        $this->jsButtonProvider = $jsButtonProvider;
    }

    /**
     * Add social login component to the review form.
     *
     * @param Form  $subject
     * @param array $result
     * @return array
     */
    public function afterGetJsComponentSocialConfig(
        \Plumrocket\AdvancedReviewAndReminder\Block\Form $subject,
        array $result
    ): array {
        unset($result['canShow']);
        $result['component'] = 'Plumrocket_SocialLoginFree/js/view/buttons';
        $result['buttons'] = $this->jsButtonProvider->getLoginButtons();
        return $result;
    }
}
