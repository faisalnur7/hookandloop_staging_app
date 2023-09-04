<?php
namespace Aheadworks\Blog\Model\Post\StructuredData;

/**
 * Class CompositeProvider
 *
 * @package Aheadworks\Blog\Model\Post\StructuredData
 */
class CompositeProvider implements ProviderInterface
{
    /**
     * @var ProviderInterface[]
     */
    private $providers;

    /**
     * @param array $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($post)
    {
        $data = [];
        foreach ($this->providers as $provider) {
            if ($provider instanceof ProviderInterface) {
                $data = array_merge($data, $provider->getData($post));
            }
        }
        return $data;
    }
}
