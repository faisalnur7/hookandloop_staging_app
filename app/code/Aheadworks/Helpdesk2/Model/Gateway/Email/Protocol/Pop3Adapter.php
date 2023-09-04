<?php
namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Protocol;

use Zend\Mail\Protocol\Pop3;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Pop3Adapter
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Protocol
 */
class Pop3Adapter implements AdapterInterface
{
    /**
     * @var Pop3
     */
    private $protocol;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $params
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $params
    ) {
        $this->protocol = $objectManager->create(Pop3::class, $params);
    }

    /**
     * @inheritdoc
     */
    public function sendRequest($xoauthString)
    {
        try {
            $this->protocol->request("AUTH " . AdapterInterface::OAUTH_NAME . ' ' . $xoauthString);
        } catch (\Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getProtocol()
    {
        return $this->protocol;
    }
}
