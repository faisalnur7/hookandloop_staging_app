<?php
namespace Aheadworks\Helpdesk2\Model\Data\Provider\Form\Ticket\Thread;

/**
 * Class ProviderInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Provider\Form
 */
class ProviderComposite implements ProviderInterface
{
    /**
     * @var ProviderInterface[]
     */
    private $providers;

    /**
     * @param ProviderInterface[] $providers
     */
    public function __construct(array $providers = [])
    {
        $this->providers = $providers;
    }

    /**
     * Provide data for form
     *
     * @param int $ticketId
     * @return array
     */
    public function getData($ticketId)
    {
        $data = [];
        foreach ($this->providers as $provider) {
            $data = array_merge_recursive($data, $provider->getData($ticketId));
        }

        return $data;
    }
}
