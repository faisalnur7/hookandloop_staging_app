<?php
declare(strict_types=1);

namespace Aheadworks\Blog\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Blog\Model\Config;
use Magento\Framework\App\RequestInterface;

class PostViewMode implements ArgumentInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ArgumentInterface
     */
    private $request;

    /**
     * @param Config $config
     * @param RequestInterface $request
     */
    public function __construct(
        Config $config,
        RequestInterface $request
    ) {
        $this->config = $config;
        $this->request = $request;
    }

    /**
     * Get grid view column count
     *
     * @return int
     */
    public function getGridViewColumnCount(): int
    {
        return $this->config->getGridViewColumnCount();
    }

    /**
     * Get grid view column count
     *
     * @return string
     */
    public function getDefaultViewMode(): string
    {
        return $this->config->getDefaultPostView();
    }

    /**
     * Get current view mode
     *
     * @return null|string
     */
    public function getCurrentViewMode(): ?string
    {
        return $this->request->getParam('post_list_mode') ?: $this->getDefaultViewMode();
    }
}