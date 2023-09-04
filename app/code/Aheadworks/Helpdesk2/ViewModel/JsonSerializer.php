<?php
namespace Aheadworks\Helpdesk2\ViewModel;

use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class JsonSerializer
 *
 * @package Aheadworks\Helpdesk2\ViewModel
 */
class JsonSerializer implements ArgumentInterface
{
    /**
     * @var Serializer
     */
    private $jsonSerializer;

    /**
     * @param Serializer $jsonSerializer
     */
    public function __construct(
        Serializer $jsonSerializer
    ) {
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Get verification result
     *
     * @param mixed $data
     * @return string
     */
    public function serialize($data)
    {
        return $this->jsonSerializer->serialize($data);
    }
}
