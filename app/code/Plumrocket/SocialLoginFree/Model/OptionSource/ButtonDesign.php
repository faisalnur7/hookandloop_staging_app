<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\OptionSource;

use Magento\Framework\View\Asset\Repository;
use Plumrocket\ExtendedAdminUi\Api\ImageRadioButtonSourceInterface;

/**
 * @since 4.0.0
 */
class ButtonDesign implements ImageRadioButtonSourceInterface
{

    public const TYPE_DEFAULT = 'default';
    public const TYPE_SQUARE = 'square';
    public const TYPE_ROUND = 'round';

    /**
     * @var Repository
     */
    private $viewAssetRepository;

    /**
     * @param \Magento\Framework\View\Asset\Repository $viewAssetRepository
     */
    public function __construct(Repository $viewAssetRepository)
    {
        $this->viewAssetRepository = $viewAssetRepository;
    }

    /**
     * Get price filter types.
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value'   => self::TYPE_DEFAULT,
                'label'   => __('Default'),
                'image' => $this->viewAssetRepository->getUrl(
                    'Plumrocket_SocialLoginFree::images/system_config/full_btn.png'
                ),
                'image2x' => $this->viewAssetRepository->getUrl(
                    'Plumrocket_SocialLoginFree::images/system_config/full_btn@2x.png'
                ),
            ],
            [
                'value'   => self::TYPE_SQUARE,
                'label'   => __('Square'),
                'image' => $this->viewAssetRepository->getUrl(
                    'Plumrocket_SocialLoginFree::images/system_config/square_btn.png'
                ),
                'image2x' => $this->viewAssetRepository->getUrl(
                    'Plumrocket_SocialLoginFree::images/system_config/square_btn@2x.png'
                ),
            ],
            [
                'value'   => self::TYPE_ROUND,
                'label'   => __('Round'),
                'image' => $this->viewAssetRepository->getUrl(
                    'Plumrocket_SocialLoginFree::images/system_config/round_btn.png'
                ),
                'image2x' => $this->viewAssetRepository->getUrl(
                    'Plumrocket_SocialLoginFree::images/system_config/round_btn@2x.png'
                ),
            ],
        ];
    }
}
